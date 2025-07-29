<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Affiche la liste des articles publiés
     */
    public function index(Request $request)
    {
        $query = Article::published()->with('author');
        
        // Recherche par titre ou contenu
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%')
                  ->orWhere('excerpt', 'like', '%' . $search . '%');
            });
        }
        
        // Filtrage par tag
        if ($request->has('tag') && $request->tag !== '') {
            $query->whereJsonContains('tags', $request->tag);
        }
        
        $articles = $query->recent()->paginate(12);
        
        // Récupérer tous les tags pour le filtre
        $allTags = Article::published()
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();
        
        return view('articles.index', compact('articles', 'allTags'));
    }

    /**
     * Affiche un article spécifique
     */
    public function show($slug)
    {
        $article = Article::published()
            ->with('author')
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Articles similaires (même tags)
        $relatedArticles = collect();
        if ($article->tags) {
            $relatedArticles = Article::published()
                ->where('id', '!=', $article->id)
                ->where(function($query) use ($article) {
                    foreach ($article->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                })
                ->recent(3)
                ->get();
        }
        
        // Si pas assez d'articles similaires, compléter avec les plus récents
        if ($relatedArticles->count() < 3) {
            $additionalArticles = Article::published()
                ->where('id', '!=', $article->id)
                ->whereNotIn('id', $relatedArticles->pluck('id'))
                ->recent(3 - $relatedArticles->count())
                ->get();
            
            $relatedArticles = $relatedArticles->merge($additionalArticles);
        }
        
        return view('articles.show', compact('article', 'relatedArticles'));
    }

    /**
     * Récupère les articles récents pour la page d'accueil
     */
    public function getRecentForHome($limit = 3)
    {
        return Article::published()
            ->with('author')
            ->recent($limit)
            ->get();
    }

    /**
     * API pour récupérer les articles (pour AJAX)
     */
    public function api(Request $request)
    {
        $query = Article::published()->with('author');
        
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }
        
        if ($request->has('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }
        
        $articles = $query->recent()->get()->map(function($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'excerpt' => $article->formatted_excerpt,
                'url' => $article->url,
                'featured_image' => $article->featured_image ? asset('storage/' . $article->featured_image) : null,
                'published_at' => $article->published_at->format('d/m/Y'),
                'author' => $article->author->name,
                'tags' => $article->tags
            ];
        });
        
        return response()->json($articles);
    }
}
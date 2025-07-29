<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        });
    }

    /**
     * Affiche la liste des articles
     */
    public function index(Request $request)
    {
        $query = Article::with('author');
        
        // Filtrage par statut
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Recherche par titre
        if ($request->has('search') && $request->search !== '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $articles = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.articles.create');
    }

    /**
     * Enregistre un nouvel article
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_description' => 'nullable|string|max:160',
            'tags' => 'nullable|string'
        ]);

        // Gestion de l'image
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('articles/images', 'public');
        }

        // Traitement des tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Attribution de l'auteur
        $validated['author_id'] = Auth::id();

        // Si publié maintenant et pas de date définie
        if ($validated['status'] === 'published' && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $article = Article::create($validated);

        return redirect()->route('administrateur.articles.index')
            ->with('success', 'Article créé avec succès.');
    }

    /**
     * Affiche un article
     */
    public function show(Article $article)
    {
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    /**
     * Met à jour un article
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_description' => 'nullable|string|max:160',
            'tags' => 'nullable|string'
        ]);

        // Gestion de l'image
        if ($request->hasFile('featured_image')) {
            // Supprimer l'ancienne image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('articles/images', 'public');
        }

        // Traitement des tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Si publié maintenant et pas de date définie
        if ($validated['status'] === 'published' && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('administrateur.articles.index')
            ->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Supprime un article
     */
    public function destroy(Article $article)
    {
        // Supprimer l'image associée
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        return redirect()->route('administrateur.articles.index')
            ->with('success', 'Article supprimé avec succès.');
    }

    /**
     * Publie rapidement un article
     */
    public function publish(Article $article)
    {
        $article->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        return back()->with('success', 'Article publié avec succès.');
    }

    /**
     * Archive un article
     */
    public function archive(Article $article)
    {
        $article->update(['status' => 'archived']);

        return back()->with('success', 'Article archivé avec succès.');
    }
}
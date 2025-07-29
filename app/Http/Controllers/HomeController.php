<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Service;
use App\Models\Prestataire;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil
     */
    public function index()
    {
        // Récupérer les derniers articles publiés pour la section actualités
        $recentArticles = Article::published()
            ->with('author')
            ->recent(3)
            ->get();
        
        // Récupérer les catégories principales pour l'affichage
        // $categories = Category::whereHas('services')
        //     ->withCount('services')
        //     ->orderBy('services_count', 'desc')
        //     ->limit(6)
        //     ->get();
        $categories = []; // Initialize as empty array to avoid errors in the view
        
        // Récupérer quelques prestataires en vedette
        $featuredPrestataires = Prestataire::where('is_approved', true)
            ->with(['user', 'services'])
            ->inRandomOrder()
            ->limit(6)
            ->get();
        
        // Récupérer les avis clients approuvés pour la section témoignages
        // $clientReviews = Review::approved()
        //     ->with(['client', 'prestataire.user'])
        //     ->where('rating', '>=', 4) // Afficher uniquement les avis positifs (4 étoiles ou plus)
        //     ->latest()
        //     ->limit(3)
        //     ->get();
        $clientReviews = [];
        
        // Statistiques générales
        $stats = [
            // 'total_prestataires' => Prestataire::where('is_approved', true)->count(),
            // 'total_services' => Service::where('status', 'active')->count(),
            // 'total_categories' => Category::count()
        ];
        
        return view('home', compact(
            'recentArticles',
            'categories', // Now an empty array
            'featuredPrestataires', // Now an empty array
            'clientReviews',
            'stats'
        ));
    }
    
    /**
     * API pour récupérer les articles récents (AJAX)
     */
    public function getRecentArticles(Request $request)
    {
        $limit = $request->get('limit', 3);
        
        $articles = Article::published()
            ->with('author')
            ->recent($limit)
            ->get()
            ->map(function($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'excerpt' => $article->formatted_excerpt,
                    'url' => route('articles.show', $article->slug),
                    'featured_image' => $article->featured_image ? asset('storage/' . $article->featured_image) : null,
                    'published_at' => $article->published_at->format('d/m/Y'),
                    'author' => $article->author->name
                ];
            });
        
        return response()->json($articles);
    }
}
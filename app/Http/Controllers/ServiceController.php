<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Affiche la liste des services publics avec filtrage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Service::with(['prestataire', 'categories'])
            ->whereHas('prestataire', function($q) {
                $q->where('is_approved', true);
            });
        
        // Recherche par mot-clé
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%')
                  ->orWhereHas('prestataire.user', function($userQuery) use ($keyword) {
                      $userQuery->where('name', 'like', '%' . $keyword . '%');
                  })
                  ->orWhereHas('categories', function($catQuery) use ($keyword) {
                      $catQuery->where('name', 'like', '%' . $keyword . '%');
                  });
            });
        }
        
        // Filtrage par catégorie
        if ($request->filled('category') && $request->category != '') {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }
        
        // Filtrage par prix
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        // Filtrage par localisation
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filtrage par disponibilité
        if ($request->filled('availability')) {
            // This is a placeholder for availability logic. 
            // You would need a more complex query based on how availability is stored.
        }

        // Filtrage pour les services premium
        if ($request->has('premium')) {
            $query->where('is_premium', true);
        }

        // Filtrage pour les prestataires avec portfolio
        if ($request->has('with_portfolio')) {
            $query->whereHas('prestataire', function ($q) {
                $q->whereNotNull('portfolio_url');
            });
        }

        // Tri
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'recent':
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $services = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        
        return view('services.index', compact('services', 'categories'));
    }

    /**
     * Affiche la liste des services du prestataire connecté.
     *
     * @return \Illuminate\View\View
     */
    public function prestataireServices()
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        if (!$prestataire) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }
        
        $services = Service::where('prestataire_id', $prestataire->id)
            ->with(['categories'])
            ->latest()
            ->get();
            
        return view('prestataire.services.index', compact('services'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau service.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        return view('prestataire.services.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        if (!$prestataire) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // Champs prix supprimés pour des raisons de confidentialité
            'duration' => 'nullable|integer|min:1',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);
        
        $service = new Service();
        $service->title = $validated['title'];
        $service->description = $validated['description'];
        // Prix supprimés pour des raisons de confidentialité
        $service->duration = $validated['duration'];
        $service->prestataire_id = $prestataire->id;
        $service->status = 'active';
        $service->save();
        
        if (isset($validated['categories'])) {
            $service->categories()->sync($validated['categories']);
        }
        
        return redirect()->route('prestataire.services.index')
            ->with('success', 'Service créé avec succès.');
    }

    /**
     * Affiche un service spécifique.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\View\View
     */
    public function show(Service $service)
    {
        $service->load(['prestataire.user', 'categories', 'reviews.client.user', 'images']);

        // Incrémenter le compteur de vues
        $service->increment('views');

        // Récupérer les services similaires de la même catégorie
        $categoryIds = $service->categories->pluck('id');

        $similarServices = Service::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })
        ->where('id', '!=', $service->id)
        ->where('is_visible', true)
        ->latest()
        ->take(4)
        ->get();

        // Calculer la note moyenne
        $averageRating = $service->reviews->avg('rating');
        $totalReviews = $service->reviews->count();

        return view('services.show', compact('service', 'similarServices', 'averageRating', 'totalReviews'));
    }

    /**
     * Affiche le formulaire d'édition d'un service.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\View\View
     */
    public function edit(Service $service)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        if (!$prestataire || $service->prestataire_id !== $prestataire->id) {
            return redirect()->route('prestataire.services.index')
                ->with('error', 'Accès non autorisé.');
        }
        
        $categories = Category::all();
        return view('prestataire.services.edit', compact('service', 'categories'));
    }

    /**
     * Met à jour un service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Service $service)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        if (!$prestataire || $service->prestataire_id !== $prestataire->id) {
            return redirect()->route('prestataire.services.index')
                ->with('error', 'Accès non autorisé.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0|gte:price_min',
            'duration' => 'nullable|integer|min:1',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);
        
        $service->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            // Prix supprimés pour des raisons de confidentialité
            'duration' => $validated['duration']
        ]);
        
        if (isset($validated['categories'])) {
            $service->categories()->sync($validated['categories']);
        }
        
        return redirect()->route('prestataire.services.index')
            ->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * Supprime un service.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Service $service)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        if (!$prestataire || $service->prestataire_id !== $prestataire->id) {
            return redirect()->route('prestataire.services.index')
                ->with('error', 'Accès non autorisé.');
        }
        
        $service->delete();
        
        return redirect()->route('prestataire.services.index')
            ->with('success', 'Service supprimé avec succès.');
    }
}
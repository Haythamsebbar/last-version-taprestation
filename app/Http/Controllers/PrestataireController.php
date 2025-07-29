<?php

namespace App\Http\Controllers;

use App\Models\Prestataire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\Category;

class PrestataireController extends Controller
{
    /**
     * Affiche la liste des prestataires approuvés.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Prestataire::with(['user', 'skills', 'services'])
            ->where('is_approved', true);
        
        // Filtrage par nom
        if ($request->has('name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }
        
        // Filtrage par secteur d'activité
        if ($request->has('secteur')) {
            $query->where('secteur_activite', 'like', '%' . $request->secteur . '%');
        }
        
        // Filtrage par compétence
        if ($request->has('skill')) {
            $query->whereHas('skills', function($q) use ($request) {
                $q->where('skills.id', $request->skill);
            });
        }
        
        // Filtrage par catégorie de service
        if ($request->has('category')) {
            $query->whereHas('services', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }
        
        $prestataires = $query->paginate(12);
        
        // Récupérer les catégories pour le filtre
        $categories = Category::orderBy('name')->get();
        
        return view('prestataires.index', compact('prestataires', 'categories'));
    }

    /**
     * Affiche le profil public d'un prestataire.
     *
     * @param  \App\Models\Prestataire  $prestataire
     * @return \Illuminate\View\View
     */
    public function show(Prestataire $prestataire)
    {
        // Vérifier que le prestataire est approuvé
        if (!$prestataire->is_approved) {
            abort(404);
        }
        
        // Load the basic relationships
        $prestataire->load(['user', 'skills', 'services']);
        
        // Load the reviews separately with proper eager loading
        $prestataire->load(['reviews' => function($query) {
            $query->with(['client', 'service'])->latest()->take(5);
        }]);
        
        // Récupérer les services similaires d'autres prestataires
        // Obtenir d'abord les IDs des services du prestataire
        $serviceIds = $prestataire->services->pluck('id')->toArray();
        
        // Obtenir les IDs des catégories associées à ces services
        $categoryIds = \DB::table('service_category')
            ->whereIn('service_id', $serviceIds)
            ->pluck('category_id')
            ->unique()
            ->toArray();
            
        $similarServices = Service::whereHas('categories', function($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })
        ->where('prestataire_id', '!=', $prestataire->id)
        ->with('prestataire.user')
        ->take(4)
        ->get();
        
        return view('prestataires.show', compact('prestataire', 'similarServices'));
    }


}
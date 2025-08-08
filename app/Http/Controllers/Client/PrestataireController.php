<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prestataire;
use App\Models\Category;
use App\Models\Skill;
use App\Models\Service;
use App\Models\Review;

class PrestataireController extends Controller
{
    /**
     * Display a listing of prestataires for browsing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Prestataire::with(['user', 'skills', 'services.category', 'reviews'])
            ->where('is_approved', true)
            ->where('is_active', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            })->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->filled('category')) {
            $categoryId = $request->get('category');
            $query->whereHas('services', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        
        // Filter by subcategory
        if ($request->filled('subcategory')) {
            $subcategoryId = $request->get('subcategory');
            $query->whereHas('services', function ($q) use ($subcategoryId) {
                $q->where('category_id', $subcategoryId);
            });
        }

        // Filter by skill
        if ($request->filled('skill')) {
            $skillId = $request->get('skill');
            $query->whereHas('skills', function ($q) use ($skillId) {
                $q->where('skill_id', $skillId);
            });
        }

        // Filter by location
        if ($request->filled('location')) {
            $location = $request->get('location');
            $query->where('city', 'like', "%{$location}%");
        }
        
        // Filter by region
        if ($request->filled('region')) {
            $region = $request->get('region');
            // Map regions to cities/departments for filtering
            $regionCities = $this->getRegionCities($region);
            if (!empty($regionCities)) {
                $query->where(function($q) use ($regionCities) {
                    foreach ($regionCities as $city) {
                        $q->orWhere('city', 'like', "%{$city}%")
                          ->orWhere('address', 'like', "%{$city}%");
                    }
                });
            }
        }
        
        // Filter by city
        if ($request->filled('city')) {
            $city = $request->get('city');
            $query->where(function($q) use ($city) {
                $q->where('city', 'like', "%{$city}%")
                  ->orWhere('address', 'like', "%{$city}%");
            });
        }

        // Filter by rating
        if ($request->filled('min_rating')) {
            $minRating = $request->get('min_rating');
            $query->whereHas('reviews', function ($q) use ($minRating) {
                $q->selectRaw('AVG(rating) as avg_rating')
                  ->groupBy('prestataire_id')
                  ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }
        
        // Filter by geographic proximity
        if ($request->filled('user_latitude') && $request->filled('user_longitude') && $request->filled('radius')) {
            $userLatitude = $request->get('user_latitude');
            $userLongitude = $request->get('user_longitude');
            $radius = $request->get('radius');
            
            $query->selectRaw(
                'prestataires.*, ( 6371 * acos( cos( radians(?) ) * 
                cos( radians( latitude ) ) * 
                cos( radians( longitude ) - radians(?) ) + 
                sin( radians(?) ) * 
                sin( radians( latitude ) ) ) ) AS distance',
                [$userLatitude, $userLongitude, $userLatitude]
            )
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc');
        }

        // Filtres par fourchette de prix - SUPPRIMÉS pour confidentialité
        // Logique de filtrage par prix supprimée pour des raisons de confidentialité

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        switch ($sortBy) {
            case 'rating':
                $query->withAvg('reviews', 'rating')
                      ->orderBy('reviews_avg_rating', $sortOrder);
                break;
            case 'reviews_count':
                $query->withCount('reviews')
                      ->orderBy('reviews_count', $sortOrder);
                break;
            case 'name':
                $query->join('users', 'prestataires.user_id', '=', 'users.id')
                      ->orderBy('users.nom', $sortOrder)
                      ->select('prestataires.*');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $prestataires = $query->paginate(12)->withQueryString();

        // Add average rating and reviews count to each prestataire
        $prestataires->getCollection()->transform(function ($prestataire) {
            $prestataire->average_rating = $prestataire->reviews()->avg('rating') ?? 0;
            $prestataire->reviews_count = $prestataire->reviews()->count();
            // Calcul des prix min/max supprimé pour confidentialité
            // $prestataire->min_price = $prestataire->services()->min('price') ?? 0;
            // $prestataire->max_price = $prestataire->services()->max('price') ?? 0;
            return $prestataire;
        });

        // Get filter options
        $categories = Category::whereHas('services.prestataire', function ($q) {
            $q->where('is_approved', true)->where('is_active', true);
        })->get();

        $skills = Skill::whereHas('prestataires', function ($q) {
            $q->where('is_approved', true)->where('is_active', true);
        })->get();

        // Get popular locations
        $locations = Prestataire::where('is_approved', true)
            ->where('is_active', true)
            ->whereNotNull('city')
            ->pluck('city')
            ->unique()
            ->take(10);

        // Statistics
        $stats = [
            'total_prestataires' => $prestataires->total(),
            'average_rating' => Review::whereHas('prestataire', function ($q) {
                $q->where('is_approved', true)->where('is_active', true);
            })->avg('rating') ?? 0,
            'total_services' => Service::whereHas('prestataire', function ($q) {
                $q->where('is_approved', true)->where('is_active', true);
            })->count(),
        ];

        // Get sectors (empty for now since no sector column exists)
        $sectors = collect();

        // Get current filters for form state
        $filters = $request->only(['sector', 'skill', 'category', 'subcategory', 'region', 'city', 'location', 'min_rating', 'min_price', 'max_price', 'user_location', 'user_latitude', 'user_longitude', 'radius']);

        return view('client.prestataires.index', compact(
            'prestataires',
            'categories',
            'skills',
            'locations',
            'stats',
            'sectors',
            'filters'
        ));
    }

    /**
     * Display the specified prestataire.
     *
     * @param  \App\Models\Prestataire  $prestataire
     * @return \Illuminate\View\View
     */
    public function show(Prestataire $prestataire)
    {
        if (!$prestataire->is_approved || !$prestataire->is_active) {
            abort(404);
        }

        $prestataire->load([
            'user',
            'skills',
            'services.category',
            'reviews.client.user',
            'availabilities'
        ])
        ->loadCount('reviews')
        ->loadAvg('reviews', 'rating');

        // Calculate statistics
        $stats = [
            'average_rating' => $prestataire->reviews_avg_rating ?? 0,
            'total_reviews' => $prestataire->reviews_count ?? 0,
            'total_services' => $prestataire->services()->count(),
            'response_time' => '< 2h', // This could be calculated from actual data
            'completion_rate' => 95, // This could be calculated from bookings
        ];

        // Get recent reviews
        $recentReviews = $prestataire->reviews()
            ->with('client.user')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Check if current user follows this prestataire
        $isFollowing = false;
        if (Auth::check() && Auth::user()->client) {
            $isFollowing = Auth::user()->client->follows()
                ->where('prestataire_id', $prestataire->id)
                ->exists();
        }

        return view('prestataires.show', compact(
            'prestataire',
            'stats',
            'recentReviews',
            'isFollowing'
        ));
    }
    
    /**
     * Get cities for a given region
     *
     * @param string $region
     * @return array
     */
    private function getRegionCities($region)
    {
        $regionMapping = [
            'ile-de-france' => ['Paris', 'Versailles', 'Créteil', 'Nanterre', 'Bobigny', 'Pontoise', 'Melun', 'Évry'],
            'auvergne-rhone-alpes' => ['Lyon', 'Grenoble', 'Saint-Étienne', 'Annecy', 'Chambéry', 'Valence', 'Clermont-Ferrand', 'Bourg-en-Bresse'],
            'nouvelle-aquitaine' => ['Bordeaux', 'Limoges', 'Poitiers', 'La Rochelle', 'Pau', 'Bayonne', 'Périgueux', 'Tulle', 'Guéret', 'Niort', 'Angoulême', 'Mont-de-Marsan'],
            'occitanie' => ['Toulouse', 'Montpellier', 'Nîmes', 'Perpignan', 'Béziers', 'Narbonne', 'Carcassonne', 'Albi', 'Castres', 'Tarbes', 'Auch', 'Cahors', 'Rodez', 'Mende', 'Foix'],
            'hauts-de-france' => ['Lille', 'Amiens', 'Arras', 'Beauvais', 'Laon'],
            'grand-est' => ['Strasbourg', 'Reims', 'Metz', 'Nancy', 'Mulhouse', 'Troyes', 'Châlons-en-Champagne', 'Charleville-Mézières', 'Bar-le-Duc', 'Épinal', 'Colmar'],
            'provence-alpes-cote-azur' => ['Marseille', 'Nice', 'Toulon', 'Aix-en-Provence', 'Avignon', 'Cannes', 'Antibes', 'Draguignan', 'Brignoles', 'Grasse', 'Digne-les-Bains', 'Gap'],
            'pays-de-la-loire' => ['Nantes', 'Angers', 'Le Mans', 'La Roche-sur-Yon', 'Laval'],
            'bretagne' => ['Rennes', 'Brest', 'Quimper', 'Lorient', 'Saint-Brieuc', 'Vannes'],
            'normandie' => ['Rouen', 'Caen', 'Le Havre', 'Cherbourg', 'Évreux', 'Alençon', 'Saint-Lô', 'Coutances', 'Avranches'],
            'bourgogne-franche-comte' => ['Dijon', 'Besançon', 'Belfort', 'Mâcon', 'Chalon-sur-Saône', 'Nevers', 'Auxerre', 'Lons-le-Saunier', 'Vesoul'],
            'centre-val-de-loire' => ['Orléans', 'Tours', 'Bourges', 'Chartres', 'Blois', 'Châteauroux'],
            'corse' => ['Ajaccio', 'Bastia', 'Corte', 'Porto-Vecchio', 'Calvi', 'Bonifacio']
        ];
        
        return $regionMapping[$region] ?? [];
    }
}
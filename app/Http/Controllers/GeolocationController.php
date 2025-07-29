<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Prestataire;
use App\Models\Client;
use App\Models\User;

class GeolocationController extends Controller
{
    /**
     * Met à jour la localisation de l'utilisateur connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $locationData = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'city' => $request->city,
            'region' => $request->region,
            'country' => $request->country,
            'location_updated_at' => now()
        ];

        try {
            // Mettre à jour selon le type d'utilisateur
            if ($user->role === 'prestataire' && $user->prestataire) {
                $user->prestataire->update($locationData);
            } elseif ($user->role === 'client' && $user->client) {
                $user->client->update($locationData);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil utilisateur non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Localisation mise à jour avec succès',
                'data' => $locationData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la localisation'
            ], 500);
        }
    }

    /**
     * Récupère les prestataires à proximité.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNearbyPrestataires(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100', // en kilomètres
            'service_id' => 'nullable|exists:services,id',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10; // 10km par défaut
        $serviceId = $request->service_id;
        $limit = $request->limit ?? 20;

        try {
            $query = Prestataire::select([
                'prestataires.*',
                DB::raw("(
                    6371 * acos(
                        cos(radians($latitude)) * 
                        cos(radians(latitude)) * 
                        cos(radians(longitude) - radians($longitude)) + 
                        sin(radians($latitude)) * 
                        sin(radians(latitude))
                    )
                ) AS distance")
            ])
            ->with(['user', 'services'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('is_visible', true)
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc');

            // Filtrer par service si spécifié
            if ($serviceId) {
                $query->whereHas('services', function ($q) use ($serviceId) {
                    $q->where('services.id', $serviceId);
                });
            }

            $prestataires = $query->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => $prestataires->map(function ($prestataire) {
                    return [
                        'id' => $prestataire->id,
                        'name' => $prestataire->user->name,
                        'company_name' => $prestataire->company_name,
                        'description' => $prestataire->description,
                        'avatar' => $prestataire->avatar_url,
                        'rating' => $prestataire->average_rating,
                        'reviews_count' => $prestataire->reviews_count,
                        'services' => $prestataire->services->pluck('name'),
                        'location' => [
                            'latitude' => $prestataire->latitude,
                            'longitude' => $prestataire->longitude,
                            'address' => $prestataire->address,
                            'city' => $prestataire->city,
                            'region' => $prestataire->region
                        ],
                        'distance' => round($prestataire->distance, 2),
                        'profile_url' => route('prestataires.show', $prestataire->id)
                    ];
                }),
                'meta' => [
                    'total' => $prestataires->count(),
                    'radius' => $radius,
                    'center' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche des prestataires à proximité'
            ], 500);
        }
    }

    /**
     * Calcule la distance entre l'utilisateur et un prestataire.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Prestataire  $prestataire
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistance(Request $request, Prestataire $prestataire)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$prestataire->latitude || !$prestataire->longitude) {
            return response()->json([
                'success' => false,
                'message' => 'Localisation du prestataire non disponible'
            ], 404);
        }

        $userLat = $request->latitude;
        $userLng = $request->longitude;
        $prestLat = $prestataire->latitude;
        $prestLng = $prestataire->longitude;

        // Formule de Haversine pour calculer la distance
        $earthRadius = 6371; // Rayon de la Terre en kilomètres
        
        $latDelta = deg2rad($prestLat - $userLat);
        $lngDelta = deg2rad($prestLng - $userLng);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($userLat)) * cos(deg2rad($prestLat)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return response()->json([
            'success' => true,
            'data' => [
                'distance_km' => round($distance, 2),
                'distance_miles' => round($distance * 0.621371, 2),
                'prestataire' => [
                    'id' => $prestataire->id,
                    'name' => $prestataire->user->name,
                    'location' => [
                        'latitude' => $prestataire->latitude,
                        'longitude' => $prestataire->longitude,
                        'address' => $prestataire->address,
                        'city' => $prestataire->city
                    ]
                ]
            ]
        ]);
    }

    /**
     * Récupère la liste des villes disponibles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(Request $request)
    {
        $search = $request->get('search', '');
        $region = $request->get('region');

        try {
            $query = DB::table('prestataires')
                ->select('city')
                ->whereNotNull('city')
                ->where('city', '!=', '')
                ->distinct();

            if ($search) {
                $query->where('city', 'LIKE', '%' . $search . '%');
            }

            if ($region) {
                $query->where('region', $region);
            }

            $cities = $query->orderBy('city')
                ->limit(50)
                ->pluck('city')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $cities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des villes'
            ], 500);
        }
    }

    /**
     * Récupère la liste des régions disponibles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegions(Request $request)
    {
        $search = $request->get('search', '');

        try {
            $query = DB::table('prestataires')
                ->select('region')
                ->whereNotNull('region')
                ->where('region', '!=', '')
                ->distinct();

            if ($search) {
                $query->where('region', 'LIKE', '%' . $search . '%');
            }

            $regions = $query->orderBy('region')
                ->limit(50)
                ->pluck('region')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $regions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des régions'
            ], 500);
        }
    }
}
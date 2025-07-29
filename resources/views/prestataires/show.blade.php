@extends('layouts.app')

@section('content')
<style>
    .profile-banner {
        background: linear-gradient(90deg, rgba(79, 70, 229, 1) 0%, rgba(129, 140, 248, 1) 100%);
        padding: 2rem;
        border-radius: 0.5rem;
        color: white;
        position: relative;
    }
    .info-card {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }
    .service-card {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }
    .service-card:hover {
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
    .stat-card {
        background-color: white;
        border-radius: 0.5rem;
        padding: 1rem;
        text-align: center;
        border: 1px solid #e5e7eb;
    }
</style>

<div class="container mx-auto px-4 py-8">

    <!-- 1. Bannière de profil -->
    <div class="profile-banner mb-8 flex items-center justify-between">
        <div class="flex items-center">
            <div class="relative mr-6">
                @if($prestataire->photo)
                    <img class="h-20 w-20 rounded-full object-cover" src="{{ asset('storage/' . $prestataire->photo) }}" alt="Photo de profil">
                @elseif($prestataire->user->avatar)
                    <img class="h-20 w-20 rounded-full object-cover" src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="Photo de profil">
                @elseif($prestataire->user->profile_photo_url)
                    <img class="h-20 w-20 rounded-full object-cover" src="{{ $prestataire->user->profile_photo_url }}" alt="Photo de profil">
                @else
                    <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center">
                        <svg class="h-12 w-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endif
                @if($prestataire->isVerified())
                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-white">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold">{{ $prestataire->user->name }}</h1>
                    @if($prestataire->isVerified())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="-ml-1 mr-1.5 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Vérifié
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @if($prestataire->services->isNotEmpty())
            <div class="text-right">
                <p class="text-lg">À partir de</p>
                <p class="text-2xl font-bold">{{ $prestataire->services->min('price') }}€/h</p>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Colonne principale (gauche) -->
        <div class="lg:col-span-2">
            <!-- 2. Section « À propos » -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
                <h2 class="text-2xl font-bold mb-4">À propos</h2>
                <p class="text-gray-700 leading-relaxed">
                    {{ $prestataire->description ?? 'Aucune description fournie.' }}
                </p>
            </div>

            <!-- 3. Services proposés -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4">Services proposés</h2>
                @if($prestataire->services->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($prestataire->services as $service)
                            <div class="service-card bg-white shadow-sm">
                                <a href="{{ route('services.show', $service) }}">
                                    @if($service->images->isNotEmpty())
                                        <img class="h-48 w-full object-cover" src="{{ Storage::url($service->images->first()->image_path) }}" alt="{{ $service->title }}">
                                    @else
                                        <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l-1.586-1.586a2 2 0 00-2.828 0L6 14"></path></svg>
                                        </div>
                                    @endif
                                </a>
                                <div class="p-4">
                                    <h3 class="text-lg font-bold mb-2">{{ $service->title }}</h3>
                                    @if($service->categories->isNotEmpty())
                                        <p>{{ $service->categories->first()->name }}</p>
                                    @endif
                                    <a href="{{ route('services.show', $service) }}">Voir détails →</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Ce prestataire ne propose aucun service pour le moment.</p>
                @endif
            </div>

            <!-- 5. Statistiques et performances -->
            <div>
                <h2 class="text-2xl font-bold mb-4">Performance & Avis</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="stat-card">
                        <p class="text-2xl font-bold text-indigo-600">{{ $prestataire->completed_projects_count ?? 0 }}</p>
                        <p class="text-gray-600">Projets complétés</p>
                    </div>
                    <div class="stat-card">
                        <p class="text-2xl font-bold text-indigo-600">{{ number_format($prestataire->reviews->avg('rating'), 1) }}/5</p>
                        <p class="text-gray-600">Avis clients</p>
                    </div>
                    <div class="stat-card">
                        <p class="text-2xl font-bold text-indigo-600">{{ $prestataire->response_rate ?? 'N/A' }}%</p>
                        <p class="text-gray-600">Taux de réponse</p>
                    </div>
                    <div class="stat-card">
                        <p class="text-2xl font-bold text-indigo-600">{{ $prestataire->satisfaction_rate ?? 'N/A' }}%</p>
                        <p class="text-gray-600">Taux de satisfaction</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Colonne de droite -->
        <div class="lg:col-span-1">
            <div class="info-card sticky top-8">
                <h3 class="text-xl font-bold mb-4">Informations</h3>
                <ul>
                    <li class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ $prestataire->user->city ?? 'Non spécifiée' }}, {{ $prestataire->user->postal_code ?? '' }}</span>
                    </li>
                    <li class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01"></path></svg>
                        <span>
                            @if($prestataire->services->isNotEmpty())
                                {{ $prestataire->services->min('price') }}€ - {{ $prestataire->services->max('price') }}€ / heure
                            @else
                                Tarifs non spécifiés
                            @endif
                        </span>
                    </li>
                    <li class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-md text-sm">{{ $prestataire->experience_years ?? '1' }} ans d'expérience</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="h-6 w-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Membre depuis {{ $prestataire->created_at->format('F Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

                        </div>
                    </div>
                    
                    <!-- Informations financières -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h2 class="section-title">Informations financières</h2>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Tarif horaire moyen</p>
                                        @php
                                            // Calculer le tarif horaire moyen basé sur les services (simulé ici)
                                            $hourlyRate = $prestataire->hourly_rate ?? rand(35, 120);
                                        @endphp
                                        <p class="text-xl font-semibold text-yellow-700">{{ $hourlyRate }}€/h</p>
                                    </div>
                                    <div class="bg-yellow-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-teal-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Revenu total</p>
                                        @php
                                            // Revenu total supprimé pour confidentialité
                                            // $totalRevenue = $prestataire->total_revenue ?? rand(5000, 50000);
                                            // $formattedRevenue = number_format($totalRevenue, 0, ',', ' ');
                                        @endphp
                                        <p class="text-xl font-semibold text-teal-700"><!-- Revenu supprimé --></p>
                                    </div>
                                    <div class="bg-teal-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-pink-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Projets en cours</p>
                                        @php
                                            // Nombre de projets en cours (simulé ici)
                                            $activeProjects = $prestataire->active_projects ?? rand(1, 5);
                                        @endphp
                                        <p class="text-xl font-semibold text-pink-700">{{ $activeProjects }}</p>
                                    </div>
                                    <div class="bg-pink-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Délai moyen de livraison</p>
                                        @php
                                            // Délai moyen de livraison (simulé ici)
                                            $deliveryTime = $prestataire->delivery_time ?? rand(3, 15);
                                        @endphp
                                        <p class="text-xl font-semibold text-orange-700">{{ $deliveryTime }} jours</p>
                                    </div>
                                    <div class="bg-orange-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                    
                    <!-- Certifications et qualifications -->
                    @if($prestataire->certifications && count($prestataire->certifications) > 0)
                        <div class="mb-6">
                            <h2 class="section-title">Certifications et qualifications</h2>
                            <div class="grid grid-cols-1 gap-3 mt-3">
                                @foreach($prestataire->certifications as $certification)
                                    <div class="bg-blue-50 p-3 rounded-lg flex items-start">
                                        <div class="text-blue-600 mr-3 mt-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $certification }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Compétences professionnelles -->
                    @if($prestataire->skills && $prestataire->skills->count() > 0)
                        <div class="mb-6">
                            <h2 class="section-title">Compétences professionnelles</h2>
                            <div class="grid grid-cols-1 gap-4 mt-3">
                                @foreach($prestataire->skills as $skill)
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                                        <div class="flex justify-between items-center mb-2">
                                            <h3 class="font-medium text-gray-800">{{ $skill->name }}</h3>
                                            <div class="flex">
                                                @php
                                                    // Simuler un niveau d'expertise basé sur l'expérience et les avis
                                                    // Dans un cas réel, cela viendrait de la base de données
                                                    $skillLevels = ['Débutant', 'Intermédiaire', 'Avancé', 'Expert'];
                                                    $randomIndex = array_rand($skillLevels);
                                                    $level = $skillLevels[$randomIndex];
                                                    
                                                    // Déterminer la couleur en fonction du niveau
                                                    $colors = [
                                                        'Débutant' => 'bg-blue-100 text-blue-800',
                                                        'Intermédiaire' => 'bg-green-100 text-green-800',
                                                        'Avancé' => 'bg-purple-100 text-purple-800',
                                                        'Expert' => 'bg-indigo-100 text-indigo-800'
                                                    ];
                                                    $color = $colors[$level];
                                                @endphp
                                                <span class="px-2 py-1 text-xs rounded-full {{ $color }}">{{ $level }}</span>
                                            </div>
                                        </div>
                                        @if($skill->description)
                                            <p class="text-sm text-gray-600">{{ $skill->description }}</p>
                                        @endif
                                        <div class="mt-2">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                @php
                                                    // Simuler une barre de progression basée sur le niveau
                                                    $percentages = [
                                                        'Débutant' => 25,
                                                        'Intermédiaire' => 50,
                                                        'Avancé' => 75,
                                                        'Expert' => 100
                                                    ];
                                                    $percentage = $percentages[$level];
                                                    
                                                    // Déterminer la couleur de la barre de progression
                                                    $barColors = [
                                                        'Débutant' => 'bg-blue-600',
                                                        'Intermédiaire' => 'bg-green-600',
                                                        'Avancé' => 'bg-purple-600',
                                                        'Expert' => 'bg-indigo-600'
                                                    ];
                                                    $barColor = $barColors[$level];
                                                @endphp
                                                <div class="{{ $barColor }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Projets réalisés -->
                    @if($prestataire->portfolio_images || $prestataire->total_projects > 0)
                        <div class="mb-6">
                            <h2 class="section-title">Projets réalisés</h2>
                            
                            @if($prestataire->portfolio_images)
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-3">
                                    @foreach(json_decode($prestataire->portfolio_images) as $image)
                                        <a href="{{ asset('storage/' . $image) }}" target="_blank" class="block rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                            <img src="{{ asset('storage/' . $image) }}" alt="Projet réalisé" class="w-full h-40 object-cover">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="mt-4">
                                <p class="text-gray-700">
                                    <span class="font-medium">{{ $prestataire->total_projects ?? 0 }}</span> projets réalisés
                                </p>
                                
                                @if($prestataire->portfolio_url)
                                    <a href="{{ $prestataire->portfolio_url }}" target="_blank" class="inline-flex items-center mt-2 text-indigo-600 hover:text-indigo-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Voir le portfolio complet
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Disponibilités -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h2 class="section-title">Disponibilités</h2>
                        
                        <div class="mt-4">
                            @php
                                $daysOfWeek = [
                                    1 => 'Lundi',
                                    2 => 'Mardi',
                                    3 => 'Mercredi',
                                    4 => 'Jeudi',
                                    5 => 'Vendredi',
                                    6 => 'Samedi',
                                    0 => 'Dimanche',
                                ];
                                
                                // Récupérer les disponibilités du prestataire
                                $availabilities = $prestataire->availabilities ?? collect();
                            @endphp
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($daysOfWeek as $dayNum => $dayName)
                                    <div class="flex items-center justify-between border-b pb-2">
                                        <span class="font-medium">{{ $dayName }}</span>
                                        <span>
                                            @php
                                                $dayAvailabilities = $availabilities->where('day_of_week', $dayNum);
                                            @endphp
                                            
                                            @if($dayAvailabilities->count() > 0)
                                                @foreach($dayAvailabilities as $availability)
                                                    <span class="text-sm text-gray-700">
                                                        {{ substr($availability->start_time, 0, 5) }} - {{ substr($availability->end_time, 0, 5) }}
                                                    </span>
                                                    @if(!$loop->last)
                                                        <span class="mx-1">|</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-sm text-gray-500">Non disponible</span>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4 text-sm text-gray-600">
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Temps de réponse moyen: <span class="font-medium ml-1">{{ $prestataire->response_time ?? 'Non spécifié' }}</span>
                                </p>
                                
                                @if($prestataire->last_active_at)
                                    <p class="flex items-center mt-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                        </svg>
                                        Dernière connexion: <span class="font-medium ml-1">{{ $prestataire->last_active_at->diffForHumans() }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Réseaux sociaux et liens -->
                    @if($prestataire->website || $prestataire->portfolio_url || $prestataire->facebook || $prestataire->instagram || $prestataire->linkedin)
                        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                            <h2 class="section-title">Réseaux sociaux et liens</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                @if($prestataire->website)
                                    <a href="{{ $prestataire->website }}" target="_blank" class="flex items-center text-gray-700 hover:text-indigo-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"></path>
                                        </svg>
                                        Site web
                                    </a>
                                @endif
                                
                                @if($prestataire->portfolio_url)
                                    <a href="{{ $prestataire->portfolio_url }}" target="_blank" class="flex items-center text-gray-700 hover:text-indigo-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                                        </svg>
                                        Portfolio
                                    </a>
                                @endif
                                
                                @if($prestataire->linkedin)
                                    <a href="{{ $prestataire->linkedin }}" target="_blank" class="flex items-center text-gray-700 hover:text-indigo-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-1-.02-2.285-1.39-2.285-1.39 0-1.6 1.087-1.6 2.21v4.253h-2.667V8.5h2.56v1.17h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v3.963zM5.5 7.33a1.668 1.668 0 110-3.336 1.668 1.668 0 010 3.336zm1.33 9.008H4.17V8.5h2.66v7.838zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.404C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.298V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"></path>
                                        </svg>
                                        LinkedIn
                                    </a>
                                @endif
                                
                                @if($prestataire->facebook)
                                    <a href="{{ $prestataire->facebook }}" target="_blank" class="flex items-center text-gray-700 hover:text-indigo-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"></path>
                                        </svg>
                                        Facebook
                                    </a>
                                @endif
                                
                                @if($prestataire->instagram)
                                    <a href="{{ $prestataire->instagram }}" target="_blank" class="flex items-center text-gray-700 hover:text-indigo-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z" clip-rule="evenodd"></path>
                                        </svg>
                                        Instagram
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($similarServices->count() > 0)
                        <div class="similar-services">
                            <h2 class="section-title">Services similaires</h2>
                            
                            <div class="similar-services-list">
                                @foreach($similarServices as $service)
                                    <div class="similar-service-card">
                                        <div class="similar-service-category-icon">
                                            @php
                                                $categoryIcons = [
                                                    'Développement web' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>',
                                                    'Design' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>',
                                                    'Marketing' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>',
                                                    'Rédaction' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>',
                                                    'default' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>'
                                                ];
                                                
                                                $category = $service->categories->first()->name ?? 'default';
                                                $icon = $categoryIcons[$category] ?? $categoryIcons['default'];
                                                echo $icon;
                                            @endphp
                                        </div>
                                        <div class="similar-service-content">
                                            <h3 class="similar-service-title">
                                                <a href="{{ route('services.show', $service) }}">
                                                    {{ $service->title }}
                                                </a>
                                            </h3>
                                            <p class="similar-service-provider">par {{ $service->prestataire->user->name }}</p>
                                            <div class="similar-service-footer">
                                                <span class="similar-service-price">{{ $service->price }} €</span>
                                                <a href="{{ route('services.show', $service) }}" class="similar-service-link">
                                                    Voir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
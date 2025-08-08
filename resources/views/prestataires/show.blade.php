@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/prestataires-list.css') }}">
<div class="container mx-auto px-4 py-8">
    <!-- En-tête du profil prestataire -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-xl p-10 mb-8 text-white relative overflow-hidden">
        <!-- Motif de fond décoratif -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
        
        <div class="relative z-10">
            <!-- Layout principal en trois colonnes -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- Colonne gauche : Photo + Évaluations (mobile: en haut) -->
                <div class="lg:col-span-3 flex flex-col items-center lg:items-start space-y-6">
                    <!-- Photo de profil agrandie -->
                    <div class="prestataire-avatar relative flex-shrink-0">
                        @if($prestataire->photo)
                            <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="{{ $prestataire->user->name }}" class="h-48 w-48 lg:h-56 lg:w-56 object-cover rounded-full border-6 border-white shadow-2xl ring-4 ring-white/20">
                        @elseif($prestataire->user->avatar)
                            <img src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="{{ $prestataire->user->name }}" class="h-48 w-48 lg:h-56 lg:w-56 object-cover rounded-full border-6 border-white shadow-2xl ring-4 ring-white/20">
                        @elseif($prestataire->user->profile_photo_url)
                            <img src="{{ $prestataire->user->profile_photo_url }}" alt="{{ $prestataire->user->name }}" class="h-48 w-48 lg:h-56 lg:w-56 object-cover rounded-full border-6 border-white shadow-2xl ring-4 ring-white/20">
                        @else
                            <div class="h-48 w-48 lg:h-56 lg:w-56 flex items-center justify-center bg-white text-blue-600 rounded-full border-6 border-white shadow-2xl ring-4 ring-white/20">
                                <svg class="h-24 w-24 lg:h-28 lg:w-28" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                                </svg>
                            </div>
                        @endif
                        @if($prestataire->isVerified())
                            <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Évaluations compactes -->
                    @php
                        $totalReviews = $prestataire->reviews->count();
                        $averageRating = $totalReviews > 0 ? $prestataire->reviews->avg('rating') : 0;
                        $roundedRating = round($averageRating, 1);
                    @endphp
                    
                    <div class="bg-yellow-500/30 backdrop-blur-sm rounded-xl p-4 border border-yellow-400/50 w-full max-w-xs">
                        @if($totalReviews > 0)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-100 mb-1">{{ $roundedRating }}</div>
                                <div class="flex justify-center items-center mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($averageRating))
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        @elseif($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <defs>
                                                    <linearGradient id="half-star-compact">
                                                        <stop offset="50%" stop-color="currentColor"/>
                                                        <stop offset="50%" stop-color="transparent"/>
                                                    </linearGradient>
                                                </defs>
                                                <path fill="url(#half-star-compact)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-yellow-200/40" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <p class="text-yellow-200/80 text-xs">{{ $totalReviews }} avis</p>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="flex justify-center items-center mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 text-yellow-200/40" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-yellow-200/60 text-xs">Aucun avis</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Colonne centrale : Nom + Description -->
                <div class="lg:col-span-6 text-center lg:text-left">
                    <h1 class="text-5xl lg:text-6xl font-black mb-4 leading-tight">{{ $prestataire->user->name }}</h1>
                    
                    @if($prestataire->isVerified())
                        <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-500/20 text-green-100 border border-green-400/30 backdrop-blur-sm mb-4">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Profil Vérifié
                        </div>
                    @endif
                    
                    <p class="text-2xl lg:text-3xl font-bold text-blue-100 mb-6 tracking-wide">{{ $prestataire->secteur_activite }}</p>
                    <p class="text-lg lg:text-xl text-blue-50/90 leading-relaxed">{{ $prestataire->description }}</p>
                </div>
                
                <!-- Colonne droite : Boutons d'action -->
                <div class="lg:col-span-3 flex flex-row gap-3">
                    @auth
                        @if(auth()->user()->isClient())
                            @if(auth()->user()->client && auth()->user()->client->isFollowing($prestataire->id))
                                <form action="{{ route('client.prestataire-follows.unfollow', $prestataire) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border-2 border-white rounded-lg text-sm font-semibold text-white bg-white/10 hover:bg-white hover:text-blue-600 transition-all duration-300 backdrop-blur-sm shadow-md hover:shadow-lg">
                                        <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        Abonné(e)
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('client.prestataire-follows.follow', $prestataire) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border-2 border-white rounded-lg text-sm font-semibold text-white bg-white/10 hover:bg-white hover:text-blue-600 transition-all duration-300 backdrop-blur-sm shadow-md hover:shadow-lg">
                                        <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        S'abonner
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('messaging.start', $prestataire) }}" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-white text-blue-600 rounded-lg text-sm font-semibold hover:bg-blue-50 transition-all duration-300 shadow-md hover:shadow-lg">
                                <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Contacter
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
        
        
        @if($prestataire->skills->count() > 0)
            <!-- Section Compétences -->
            <div class="mt-8 pt-8 border-t border-white/20">
                <h3 class="text-2xl font-bold mb-6 text-white">Compétences</h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($prestataire->skills as $skill)
                        <span class="bg-white/20 text-white text-base font-semibold px-6 py-3 rounded-full border border-white/30 backdrop-blur-sm hover:bg-white/30 transition-all duration-300">
                            {{ $skill->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
        </div>
    </div>
    
    
    
    <!-- Structure à deux colonnes -->
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Colonne gauche (70%) - Contenus métier -->
            <div class="lg:w-[70%] w-full space-y-6">
                <!-- Bloc 1: Services proposés -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center mb-6">
                        
                        <h2 class="text-2xl font-bold text-gray-800">Services proposés</h2>
                        @if($prestataire->services->count() > 0)
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $prestataire->services->count() }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        @forelse($prestataire->services->take(3) as $service)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start space-x-4">
                                    <!-- Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                        @if($service->image)
                                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Contenu -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Service
                                                </span>
                                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $service->title }}</h3>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $service->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                            {{ Str::limit($service->description, 120) }}
                                        </p>
                                        
                                        <div class="flex items-center justify-between">
                                            <span class="text-xl font-bold text-blue-600">{{ number_format($service->price, 0, ',', ' ') }} €</span>
                                            <a href="{{ route('services.show', $service) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                                Voir détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun service disponible</h3>
                                <p class="mt-1 text-sm text-gray-500">Ce prestataire n'a pas encore publié de services.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Bloc 2: Équipements disponibles à la location -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center mb-6">
                        
                        <h2 class="text-2xl font-bold text-gray-800">Équipements disponibles à la location</h2>
                        @if(isset($prestataire->equipments) && $prestataire->equipments->count() > 0)
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $prestataire->equipments->count() }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        @forelse(($prestataire->equipments ?? collect())->take(3) as $equipment)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start space-x-4">
                                    <!-- Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                        @if($equipment->main_photo)
                                            <img src="{{ asset('storage/' . $equipment->main_photo) }}" alt="{{ $equipment->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Contenu -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Équipement
                                                </span>
                                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $equipment->name }}</h3>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $equipment->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                            {{ Str::limit($equipment->description, 120) }}
                                        </p>
                                        
                                        <div class="flex items-center justify-between">
                                            <span class="text-xl font-bold text-green-600">{{ number_format($equipment->price_per_day, 0, ',', ' ') }} €/jour</span>
                                            <button class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                                Louer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun équipement disponible</h3>
                                <p class="mt-1 text-sm text-gray-500">Ce prestataire n'a pas d'équipements à louer pour le moment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Bloc 3: Offres en vente urgente -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center mb-6">
                        
                        <h2 class="text-2xl font-bold text-gray-800">Offres en vente urgente</h2>
                        @if(isset($prestataire->urgentSales) && $prestataire->urgentSales->count() > 0)
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $prestataire->urgentSales->count() }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        @forelse(($prestataire->urgentSales ?? collect())->take(3) as $sale)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start space-x-4">
                                    <!-- Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                        @if(is_array($sale->photos) && count($sale->photos) > 0)
                                            <img src="{{ asset('storage/' . $sale->photos[0]) }}" alt="{{ $sale->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Contenu -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Vente urgente
                                                </span>
                                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $sale->title }}</h3>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $sale->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                            {{ Str::limit($sale->description, 120) }}
                                        </p>
                                        
                                        <div class="flex items-center justify-between">
                                            <span class="text-xl font-bold text-red-600">{{ number_format($sale->price, 0, ',', ' ') }} €</span>
                                            <button class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                                Contacter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune vente urgente</h3>
                                <p class="mt-1 text-sm text-gray-500">Ce prestataire n'a pas de ventes urgentes en cours.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Bloc 4: Section Avis -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <!-- Bouton pour afficher le formulaire d'avis -->
                    @auth
                        @if(auth()->user()->isClient())
                            @php
                                $existingReview = auth()->user()->client->reviews()->where('prestataire_id', $prestataire->id)->first();
                                
                                // Vérifier si l'utilisateur a déjà interagi avec ce prestataire
                                $hasInteracted = false;
                                
                                // Vérifier les messages envoyés
                                $hasMessages = \App\Models\Message::where('sender_id', auth()->id())
                                    ->where('receiver_id', $prestataire->user_id)
                                    ->exists();
                                
                                // Vérifier les réservations/bookings
                                $hasBookings = \App\Models\Booking::where('client_id', auth()->user()->client->id)
                                    ->where('prestataire_id', $prestataire->id)
                                    ->exists();
                                
                                $hasInteracted = $hasMessages || $hasBookings;
                            @endphp
                            
                            @if(!$existingReview && $hasInteracted)
                                <!-- Bouton pour afficher le formulaire -->
                                <div class="mb-8">
                                    <button id="show-review-form" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                        <span>Laisser un avis</span>
                                    </button>
                                </div>
                                
                                <!-- Formulaire caché par défaut -->
                                <div id="review-form" class="mb-8 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200" style="display: none;">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-xl font-bold text-gray-800">Laisser un avis</h3>
                                        <button id="hide-review-form" class="text-gray-500 hover:text-gray-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('reviews.store') }}" method="POST" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="prestataire_id" value="{{ $prestataire->id }}">
                                        
                                        <!-- Note en étoiles -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                                            <div class="flex items-center space-x-1" id="star-rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="{{ $i }}">
                                                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                        </svg>
                                                    </button>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="rating-input" required>
                                        </div>
                                        
                                        <!-- Commentaire -->
                                        <div>
                                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Commentaire</label>
                                            <textarea name="comment" id="comment" rows="3" maxlength="300" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Partagez votre expérience avec ce prestataire (200-300 caractères)"></textarea>
                                            <div class="text-sm text-gray-500 mt-1 character-count">
                                                <span id="char-count">0</span>/300 caractères
                                            </div>
                                        </div>
                                        
                                        <!-- Bouton d'envoi -->
                                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                            Envoyer mon avis
                                        </button>
                                    </form>
                                </div>
                            @elseif(!$existingReview && !$hasInteracted)
                                <div class="mb-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <p class="text-yellow-800">Vous devez d'abord interagir avec ce prestataire (envoyer un message ou réserver un service) pour pouvoir laisser un avis.</p>
                                    </div>
                                </div>
                            @elseif($existingReview)
                                <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-green-800">Vous avez déjà évalué ce prestataire.</p>
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-gray-600">Vous devez être <a href="{{ route('login') }}" class="text-blue-600 hover:underline">connecté</a> pour laisser un avis.</p>
                        </div>
                    @endauth
                    
                    <!-- Liste des avis reçus -->
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Avis clients ({{ $prestataire->reviews->count() }})</h3>
                        
                        @if($prestataire->reviews->count() > 0)
                            <div class="space-y-4">
                                @foreach($prestataire->reviews->sortByDesc('created_at') as $review)
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-start space-x-4">
                                            <!-- Avatar de l'auteur -->
                                            <div class="flex-shrink-0">
                                                @if($review->client && $review->client->user && $review->client->user->avatar)
                                                    <img src="{{ asset('storage/' . $review->client->user->avatar) }}" 
                                                        alt="{{ $review->client->user->name }}" 
                                                        class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                        <span class="text-gray-600 font-medium text-sm">
                                                            {{ $review->client && $review->client->user ? substr($review->client->user->name, 0, 1) : '?' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Contenu de l'avis -->
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div>
                                                        <h4 class="font-medium text-gray-800">
                                                            {{ $review->client && $review->client->user ? $review->client->user->name : 'Utilisateur supprimé' }}
                                                        </h4>
                                                        <div class="flex items-center space-x-2">
                                                            <div class="flex items-center space-x-1">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">
                                                                        ⭐
                                                                    </span>
                                                                @endfor
                                                            </div>
                                                            <span class="text-sm font-medium text-gray-600">{{ number_format($review->rating, 1) }}</span>
                                                        </div>
                                                    </div>
                                                    <span class="text-sm text-gray-500">
                                                        {{ $review->created_at->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                                
                                                @if($review->comment)
                                                    <p class="text-gray-700 text-sm leading-relaxed">
                                                        {{ $review->comment }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-gray-400 text-4xl mb-4">💬</div>
                                <p class="text-gray-500">Aucun avis pour le moment</p>
                                <p class="text-sm text-gray-400 mt-1">Soyez le premier à laisser un avis!</p>
                            </div>
                        @endif
                     </div>
                </div>
            </div>
            
            <!-- Colonne droite (30%) - Informations et Vidéos -->
            <div class="lg:w-[30%] w-full">
                <div class="sticky top-8 space-y-6">
                    <!-- Section Informations de contact -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Informations de contact</h2>
                        <div class="space-y-3">
                            @if($prestataire->phone)
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    {{ $prestataire->phone }}
                                </div>
                            @endif
                            @if($prestataire->address)
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $prestataire->address }}, {{ $prestataire->city }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Section Vidéos -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                             <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                             </svg>
                             Vidéos
                             <span class="ml-2 bg-purple-100 text-purple-600 text-xs font-medium px-2 py-1 rounded-full">{{ $prestataire->videos->count() }}</span>
                         </h2>
                         
                         <!-- Swiper automatique pour vidéos -->
                         @if($prestataire->videos->count() > 0)
                             @php $limitedVideos = $prestataire->videos->take(3); @endphp
                             <div class="video-swiper-container relative w-full">
                                 <!-- Container principal -->
                                 <div class="video-swiper overflow-hidden rounded-xl w-full" style="aspect-ratio: 16/9;">
                                     <div class="video-slides flex transition-transform duration-500 ease-in-out h-full" style="width: {{ $limitedVideos->count() * 100 }}%;">
                                         @foreach($limitedVideos as $index => $video)
                                             <div class="video-slide flex-shrink-0 h-full" style="width: {{ 100 / $limitedVideos->count() }}%;" data-video-index="{{ $index }}">
                                                 <div class="relative bg-black w-full h-full">
                                                     <video 
                                                         class="video-player w-full h-full object-cover" 
                                                         src="{{ asset('storage/' . $video->video_path) }}"
                                                         {{ $index === 0 ? 'autoplay' : '' }}
                                                         muted
                                                         loop
                                                         playsinline
                                                     ></video>
                                                     
                                                     <!-- Overlay avec contrôles -->
                                                     <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent">
                                                         <!-- Bouton play/pause -->
                                                         <button class="play-pause-btn absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white opacity-0 transition-opacity duration-200">
                                                             <svg class="play-icon w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                                                 <path d="M8 5v14l11-7z"/>
                                                             </svg>
                                                             <svg class="pause-icon w-8 h-8 hidden" fill="currentColor" viewBox="0 0 24 24">
                                                                 <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                                             </svg>
                                                         </button>
                                                         
                                                         <!-- Informations vidéo -->
                                                         <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                                             <h4 class="font-semibold text-sm mb-1 line-clamp-2">{{ $video->title }}</h4>
                                                             <div class="flex items-center text-xs text-gray-300 mb-2">
                                                                 <span class="flex items-center mr-4">
                                                                     <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                     </svg>
                                                                     {{ number_format($video->views_count ?? 0) }}
                                                                 </span>
                                                                 <span class="flex items-center">
                                                                     <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                                         <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                                     </svg>
                                                                     {{ number_format($video->likes_count ?? 0) }}
                                                                 </span>
                                                             </div>
                                                             <p class="text-xs text-gray-300">{{ $video->created_at->diffForHumans() }}</p>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         @endforeach
                                     </div>
                                 </div>
                                 
                                 <!-- Contrôles de navigation -->
                                 @if($prestataire->videos->count() > 1)
                                     <!-- Boutons précédent/suivant -->
                                     <button class="swiper-btn-prev absolute left-2 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors z-10">
                                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                         </svg>
                                     </button>
                                     <button class="swiper-btn-next absolute right-2 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors z-10">
                                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                         </svg>
                                     </button>
                                     
                                     <!-- Indicateurs de pagination -->
                                     <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                                         @foreach($limitedVideos as $index => $video)
                                             <button class="swiper-dot w-2 h-2 rounded-full transition-colors {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}" data-slide="{{ $index }}"></button>
                                         @endforeach
                                     </div>
                                     
                                     <!-- Contrôle de lecture automatique -->
                                     <button class="autoplay-toggle absolute top-4 right-4 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors z-10">
                                         <svg class="autoplay-on w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                             <path d="M8 5v14l11-7z"/>
                                         </svg>
                                         <svg class="autoplay-off w-5 h-5 hidden" fill="currentColor" viewBox="0 0 24 24">
                                             <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                         </svg>
                                     </button>
                                 @endif
                             </div>
                         @else
                             <div class="text-center py-8">
                                 <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                 </svg>
                                 <h3 class="text-sm font-medium text-gray-700 mb-1">Aucune vidéo</h3>
                                 <p class="text-xs text-gray-500">Ce prestataire n'a pas encore publié de vidéos.</p>
                             </div>
                         @endif
                     </div>
                 </div>
             </div>
         </div>
     </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'affichage du formulaire d'avis
    const showFormBtn = document.getElementById('show-review-form');
    const hideFormBtn = document.getElementById('hide-review-form');
    const reviewForm = document.getElementById('review-form');
    
    if (showFormBtn && reviewForm) {
        showFormBtn.addEventListener('click', function() {
            reviewForm.style.display = 'block';
            showFormBtn.style.display = 'none';
            // Scroll vers le formulaire
            reviewForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    }
    
    if (hideFormBtn && reviewForm && showFormBtn) {
        hideFormBtn.addEventListener('click', function() {
            reviewForm.style.display = 'none';
            showFormBtn.style.display = 'block';
        });
    }
    
    // Système d'étoiles interactif
    const starButtons = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    
    if (starButtons.length > 0 && ratingInput) {
        starButtons.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                ratingInput.value = rating;
                
                // Mettre à jour l'affichage des étoiles
                starButtons.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });
            });
            
            // Effet de survol
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                starButtons.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-300');
                    }
                });
            });
            
            star.addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value) || 0;
                starButtons.forEach((s, i) => {
                    s.classList.remove('text-yellow-300');
                    if (i < currentRating) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });
            });
        });
    }
    
    // Compteur de caractères
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');
    
    if (commentTextarea && charCount) {
        commentTextarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            // Changer la couleur selon la limite
            if (count > 250) {
                charCount.classList.add('text-orange-500');
                charCount.classList.remove('text-gray-500', 'text-red-500');
            } else if (count > 280) {
                charCount.classList.add('text-red-500');
                charCount.classList.remove('text-gray-500', 'text-orange-500');
            } else {
                charCount.classList.add('text-gray-500');
                charCount.classList.remove('text-orange-500', 'text-red-500');
            }
        });
    }
    
    // Message de confirmation après soumission
    @if(session('success'))
        // Afficher une notification de succès
        const successMessage = document.createElement('div');
        successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        successMessage.textContent = '{{ session("success") }}';
        document.body.appendChild(successMessage);
        
        // Supprimer le message après 5 secondes
        setTimeout(() => {
            successMessage.remove();
        }, 5000);
    @endif
    
    // Gestion du swiper automatique pour les vidéos
    const videoSwiper = document.querySelector('.video-swiper-container');
    if (videoSwiper) {
        const videoSlides = document.querySelector('.video-slides');
        const totalSlides = document.querySelectorAll('.video-slide').length;
        const prevBtn = document.querySelector('.swiper-btn-prev');
        const nextBtn = document.querySelector('.swiper-btn-next');
        const autoplayToggle = document.querySelector('.autoplay-toggle');
        const dots = document.querySelectorAll('.swiper-dot');
        const videos = document.querySelectorAll('.video-player');
        
        let currentSlide = 0;
        let autoplayInterval;
        let isAutoplayActive = true;
        
        // Fonction pour aller à une slide spécifique
        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            const translateX = -(currentSlide * (100 / totalSlides));
            videoSlides.style.transform = `translateX(${translateX}%)`;
            
            // Mettre à jour les dots
            dots.forEach((dot, index) => {
                if (index === currentSlide) {
                    dot.classList.remove('bg-white/50');
                    dot.classList.add('bg-white');
                } else {
                    dot.classList.remove('bg-white');
                    dot.classList.add('bg-white/50');
                }
            });
            
            // Gérer la lecture des vidéos
            videos.forEach((video, index) => {
                if (index === currentSlide) {
                    video.play();
                } else {
                    video.pause();
                }
            });
        }
        
        // Fonction pour aller à la slide suivante
        function nextSlide() {
            const next = (currentSlide + 1) % totalSlides;
            goToSlide(next);
        }
        
        // Fonction pour aller à la slide précédente
        function prevSlide() {
            const prev = (currentSlide - 1 + totalSlides) % totalSlides;
            goToSlide(prev);
        }
        
        // Démarrer l'autoplay
        function startAutoplay() {
            if (totalSlides > 1) {
                autoplayInterval = setInterval(nextSlide, 5000); // Change toutes les 5 secondes
            }
        }
        
        // Arrêter l'autoplay
        function stopAutoplay() {
            clearInterval(autoplayInterval);
        }
        
        // Toggle autoplay
        function toggleAutoplay() {
            isAutoplayActive = !isAutoplayActive;
            const autoplayOnIcon = autoplayToggle.querySelector('.autoplay-on');
            const autoplayOffIcon = autoplayToggle.querySelector('.autoplay-off');
            
            if (isAutoplayActive) {
                startAutoplay();
                autoplayOnIcon.classList.remove('hidden');
                autoplayOffIcon.classList.add('hidden');
            } else {
                stopAutoplay();
                autoplayOnIcon.classList.add('hidden');
                autoplayOffIcon.classList.remove('hidden');
            }
        }
        
        // Event listeners
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                if (isAutoplayActive) {
                    stopAutoplay();
                    startAutoplay(); // Redémarrer le timer
                }
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                if (isAutoplayActive) {
                    stopAutoplay();
                    startAutoplay(); // Redémarrer le timer
                }
            });
        }
        
        if (autoplayToggle) {
            autoplayToggle.addEventListener('click', toggleAutoplay);
        }
        
        // Event listeners pour les dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
                if (isAutoplayActive) {
                    stopAutoplay();
                    startAutoplay(); // Redémarrer le timer
                }
            });
        });
        
        // Gestion des boutons play/pause pour chaque vidéo
        videos.forEach((video, index) => {
            const videoSlide = video.closest('.video-slide');
            const playPauseBtn = videoSlide.querySelector('.play-pause-btn');
            const playIcon = playPauseBtn.querySelector('.play-icon');
            const pauseIcon = playPauseBtn.querySelector('.pause-icon');
            
            // Afficher le bouton au survol
            videoSlide.addEventListener('mouseenter', () => {
                playPauseBtn.style.opacity = '1';
            });
            
            videoSlide.addEventListener('mouseleave', () => {
                playPauseBtn.style.opacity = '0';
            });
            
            // Toggle play/pause
            playPauseBtn.addEventListener('click', () => {
                if (video.paused) {
                    video.play();
                    playIcon.classList.add('hidden');
                    pauseIcon.classList.remove('hidden');
                } else {
                    video.pause();
                    playIcon.classList.remove('hidden');
                    pauseIcon.classList.add('hidden');
                }
            });
            
            // Mettre à jour les icônes selon l'état de la vidéo
            video.addEventListener('play', () => {
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
            });
            
            video.addEventListener('pause', () => {
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            });
        });
        
        // Démarrer l'autoplay au chargement
        if (totalSlides > 1) {
            startAutoplay();
        }
        
        // Pause autoplay quand l'utilisateur quitte la page
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoplay();
            } else if (isAutoplayActive) {
                startAutoplay();
            }
        });
    }
});
</script>
@endpush

@endsection

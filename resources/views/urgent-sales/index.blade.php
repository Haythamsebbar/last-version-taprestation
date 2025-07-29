@extends('layouts.app')

@section('title', 'Ventes urgentes - TaPrestation')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Bannière d'en-tête -->
    <div class="bg-red-600 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-grid-pattern"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="text-center">
                <div class="inline-flex items-center justify-center bg-white bg-opacity-25 rounded-full w-16 h-16 mb-4">
                    <i class="fas fa-bolt text-3xl text-white"></i>
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
                    Ventes Urgentes
                </h1>
                <p class="mt-4 text-xl text-red-100 max-w-2xl mx-auto">
                    Saisissez les meilleures affaires avant qu'il ne soit trop tard.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Filtres de recherche -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 rounded-xl shadow-md p-6 sticky top-4">
                    <div class="flex items-center gap-3 mb-5">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-800">Filtres</h3>
                    </div>
                    
                    <form method="GET" action="{{ route('urgent-sales.index') }}" class="space-y-5">
                        <!-- Mot-clé -->
                        <div>
                            <label for="search" class="block text-sm font-semibold text-gray-600 mb-2">Mot-clé</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                                       placeholder="Ordinateur portable, etc."
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-shadow duration-200">
                            </div>
                        </div>

                        <!-- Ville -->
                        <div>
                            <label for="city" class="block text-sm font-semibold text-gray-600 mb-2">Ville</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="city" name="city" value="{{ request('city') }}" 
                                       placeholder="Paris, Lyon..."
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-shadow duration-200">
                            </div>
                        </div>

                        <!-- État -->
                        <div>
                            <label for="condition" class="block text-sm font-semibold text-gray-600 mb-2">État</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4M17 3v4m-2-2h4m2 12v4m-2-2h4M12 3v18"></path></svg>
                                </div>
                                <select id="condition" name="condition" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-shadow duration-200">
                                    <option value="">Tous les états</option>
                                    @foreach($conditions as $value => $label)
                                        <option value="{{ $value }}" {{ request('condition') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Prix maximum -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Prix maximum</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H9a2 2 0 00-2 2v2m4 4h.01M17 13.75V21a2 2 0 01-2 2H9a2 2 0 01-2-2v-7.25A2.25 2.25 0 019.25 11h5.5A2.25 2.25 0 0117 13.75z"></path></svg>
                                </div>
                                <select class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-shadow duration-200">
                                    <option>Tous les prix</option>
                                    <option>Moins de 50€</option>
                                    <option>Moins de 100€</option>
                                    <option>Moins de 500€</option>
                                    <option>Moins de 1000€</option>
                                </select>
                            </div>
                        </div>

                        <!-- Trier par -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Trier par</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9M3 12h9m5-4v10l4-5-4-5z"></path></svg>
                                </div>
                                <select class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-shadow duration-200">
                                    <option>Pertinence</option>
                                    <option>Plus récent</option>
                                    <option>Prix croissant</option>
                                    <option>Prix décroissant</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filtres spéciaux -->
                        <div class="space-y-3 pt-4 border-t border-gray-200">
                            <label class="flex items-center">
                                <input type="checkbox" name="urgent_only" value="1" {{ request('urgent_only') ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="ml-3 text-sm text-gray-700">Ventes urgentes uniquement</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="ml-3 text-sm text-gray-700">Avec livraison</span>
                            </label>
                        </div>

                        <div class="flex flex-col gap-3 pt-5">
                            <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 font-semibold flex items-center justify-center gap-2">
                                Appliquer
                            </button>
                            <a href="{{ route('urgent-sales.index') }}" class="w-full text-center text-gray-600 hover:text-red-600 font-medium transition-colors duration-200 py-2">
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Résultats -->
            <div class="lg:col-span-3">
                <!-- Affichage des résultats -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 mt-6">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Résultats :</span>
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                            {{ $urgentSales->total() }} vente(s)
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600">Trier par :</span>
                        <select onchange="window.location.href=this.value" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'recent']) }}" {{ request('sort') == 'recent' || !request('sort') ? 'selected' : '' }}>Pertinence</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'urgent']) }}" {{ request('sort') == 'urgent' ? 'selected' : '' }}>Plus récent</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            <option>Urgence</option>
                        </select>
                    </div>
                </div>

                <!-- Ventes urgentes en vedette -->
                @if($featuredSales->count() > 0 && !request()->hasAny(['search', 'city', 'price_min', 'price_max', 'condition']))
                    <div class="bg-gradient-to-r from-red-500 to-pink-600 rounded-lg shadow-lg p-6 mb-8 text-white">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Ventes urgentes du moment
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($featuredSales as $sale)
                                <a href="{{ route('urgent-sales.show', $sale) }}" class="bg-white/10 backdrop-blur-sm rounded-lg p-4 hover:bg-white/20 transition duration-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium">{{ Str::limit($sale->title, 30) }}</span>
                                        <span class="bg-white/20 px-2 py-1 rounded text-xs font-bold">URGENT</span>
                                    </div>
                                    <div class="text-2xl font-bold">{{ number_format($sale->price, 2) }}€</div>
                                    <div class="text-sm opacity-90">{{ $sale->prestataire->user->name }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Liste des ventes -->
                @if($urgentSales->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($urgentSales as $sale)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-300 overflow-hidden">
                                <a href="{{ route('urgent-sales.show', $sale) }}" class="block">
                                    <!-- Image -->
                                    <div class="relative h-48 bg-gray-200">
                                        @if($sale->photos && count($sale->photos) > 0)
                                            <img src="{{ Storage::url($sale->photos[0]) }}" alt="{{ $sale->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        @if($sale->is_urgent)
                                            <div class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-md text-xs font-bold flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path>
                                                </svg>
                                                URGENT
                                            </div>
                                        @endif
                                        
                                        <div class="absolute top-3 right-3 bg-black/70 text-white px-2 py-1 rounded-md text-xs font-medium">
                                            {{ ucfirst($sale->condition) }}
                                        </div>
                                    </div>
                                    
                                    <!-- Contenu -->
                                    <div class="p-5">
                                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 text-lg">{{ $sale->title }}</h3>
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $sale->description }}</p>
                                        
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="text-2xl font-bold text-red-600">{{ number_format($sale->price, 2) }}€</div>
                                            @if($sale->quantity > 1)
                                                <div class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-md">Qté: {{ $sale->quantity }}</div>
                                            @endif
                                        </div>
                                        
                                       
                                        
                                        <div class="pt-3 border-t border-gray-100">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <div class="w-8 h-8 bg-gray-300 rounded-full mr-2 flex items-center justify-center">
                                                        @if($sale->prestataire->user->avatar)
                                                            <img src="{{ Storage::url($sale->prestataire->user->avatar) }}" alt="{{ $sale->prestataire->user->name }}" class="w-8 h-8 rounded-full object-cover">
                                                        @else
                                                            <span class="text-xs font-medium">{{ substr($sale->prestataire->user->name, 0, 1) }}</span>
                                                        @endif
                                                    </div>
                                                    <span class="truncate">{{ $sale->prestataire->user->name }}</span>
                                                </div>
                                                <button class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-xs font-medium">
                                                    Contacter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $urgentSales->links() }}
                    </div>
                @else
                    <!-- Message d'état vide -->
                    <div class="text-center py-16">
                        <div class="max-w-md mx-auto">
                            <div class="w-24 h-24 mx-auto mb-6 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune vente urgente trouvée</h3>
                            <p class="text-gray-600 mb-6">Nous n'avons trouvé aucune vente urgente correspondant à vos critères de recherche. Essayez de modifier vos filtres ou explorez toutes nos ventes.</p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                @if(request()->anyFilled(['search', 'city', 'condition']))
                                    <a href="{{ route('urgent-sales.index') }}" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Réinitialiser les filtres
                                    </a>
                                @else
                                    <a href="{{ route('urgent-sales.index') }}" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                        Voir toutes les ventes
                                    </a>
                                @endif
                                <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Retour à l'accueil
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
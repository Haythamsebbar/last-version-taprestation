@extends('layouts.app')

@section('title', 'Ventes urgentes')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    <i class="fas fa-bolt mr-3"></i>Ventes Urgentes
                </h1>
                <p class="text-xl md:text-2xl mb-6 opacity-90">
                    Découvrez les meilleures affaires des prestataires événementiels
                </p>
                <div class="flex justify-center items-center space-x-6 text-lg">
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span>Offres limitées</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-tag mr-2"></i>
                        <span>Prix réduits</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-shipping-fast mr-2"></i>
                        <span>Disponible rapidement</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mx-auto px-4 py-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $totalSales }}</div>
                <div class="text-gray-600">Ventes disponibles</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-red-600 mb-2">{{ $urgentSales }}</div>
                <div class="text-gray-600">Ventes urgentes</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ $newToday }}</div>
                <div class="text-gray-600">Nouvelles aujourd'hui</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $avgPrice }}€</div>
                <div class="text-gray-600">Prix moyen</div>
            </div>
        </div>
        
        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <form method="GET" action="{{ route('urgent-sales.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Recherche -->
                        <div class="lg:col-span-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par titre, description..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Localisation -->
                        <div>
                            <input type="text" name="location" value="{{ request('location') }}" placeholder="Ville, département..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Prix maximum -->
                        <div>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Prix max (€)" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- État -->
                        <div>
                            <select name="condition" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Tous les états</option>
                                <option value="new" {{ request('condition') === 'new' ? 'selected' : '' }}>Neuf</option>
                                <option value="like_new" {{ request('condition') === 'like_new' ? 'selected' : '' }}>Comme neuf</option>
                                <option value="very_good" {{ request('condition') === 'very_good' ? 'selected' : '' }}>Très bon état</option>
                                <option value="good" {{ request('condition') === 'good' ? 'selected' : '' }}>Bon état</option>
                                <option value="fair" {{ request('condition') === 'fair' ? 'selected' : '' }}>État correct</option>
                                <option value="poor" {{ request('condition') === 'poor' ? 'selected' : '' }}>Mauvais état</option>
                            </select>
                        </div>
                        
                        <!-- Tri -->
                        <div>
                            <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Plus récentes</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                                <option value="most_viewed" {{ request('sort') === 'most_viewed' ? 'selected' : '' }}>Plus vues</option>
                            </select>
                        </div>
                        
                        <!-- Filtres spéciaux -->
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="urgent_only" value="1" {{ request('urgent_only') ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Urgentes uniquement</span>
                            </label>
                        </div>
                        
                        <!-- Boutons -->
                        <div class="flex space-x-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                <i class="fas fa-search mr-2"></i>Rechercher
                            </button>
                            
                            @if(request()->hasAny(['search', 'location', 'max_price', 'condition', 'sort', 'urgent_only']))
                                <a href="{{ route('urgent-sales.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Résultats -->
        @if($sales->count() > 0)
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $sales->total() }} vente{{ $sales->total() > 1 ? 's' : '' }} trouvée{{ $sales->total() > 1 ? 's' : '' }}
                    </h2>
                    
                    @if(request()->hasAny(['search', 'location', 'max_price', 'condition', 'urgent_only']))
                        <p class="text-gray-600">
                            Résultats filtrés
                            @if(request('search'))
                                pour "{{ request('search') }}"
                            @endif
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Grille des ventes -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($sales as $sale)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden">
                        <a href="{{ route('urgent-sales.show', $sale) }}" class="block">
                            <!-- Image -->
                            <div class="relative">
                                @if($sale->photos && count($sale->photos) > 0)
                                    <img src="{{ Storage::url($sale->photos[0]) }}" alt="{{ $sale->title }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-3xl"></i>
                                    </div>
                                @endif
                                
                                <!-- Badges -->
                                <div class="absolute top-2 left-2 space-y-1">
                                    @if($sale->is_urgent)
                                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                            <i class="fas fa-bolt mr-1"></i>URGENT
                                        </span>
                                    @endif
                                    
                                    @if($sale->created_at->isToday())
                                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                            <i class="fas fa-star mr-1"></i>NOUVEAU
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Prix -->
                                <div class="absolute bottom-2 right-2">
                                    <span class="bg-black bg-opacity-75 text-white px-3 py-1 rounded-full font-bold">
                                        {{ number_format($sale->price, 0, ',', ' ') }}€
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Contenu -->
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $sale->title }}</h3>
                                
                                <div class="flex items-center text-sm text-gray-600 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <span>{{ $sale->location }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                                    <span class="bg-gray-100 px-2 py-1 rounded">{{ $sale->condition_label }}</span>
                                    <span>Qté: {{ $sale->quantity }}</span>
                                </div>
                                
                                <p class="text-gray-700 text-sm line-clamp-2 mb-3">{{ $sale->description }}</p>
                                
                                <!-- Prestataire -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $sale->prestataire->user->name }}</span>
                                    </div>
                                    
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="fas fa-eye mr-1"></i>
                                        <span>{{ $sale->views_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $sales->appends(request()->query())->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune vente trouvée</h3>
                <p class="text-gray-600 mb-6">
                    @if(request()->hasAny(['search', 'location', 'max_price', 'condition', 'urgent_only']))
                        Aucune vente ne correspond à vos critères de recherche.
                    @else
                        Il n'y a actuellement aucune vente urgente disponible.
                    @endif
                </p>
                
                @if(request()->hasAny(['search', 'location', 'max_price', 'condition', 'urgent_only']))
                    <a href="{{ route('urgent-sales.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200">
                        <i class="fas fa-refresh mr-2"></i>Voir toutes les ventes
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endpush
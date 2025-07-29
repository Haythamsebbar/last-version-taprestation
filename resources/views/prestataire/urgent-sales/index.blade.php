@extends('layouts.app')

@section('title', 'Mes Ventes Urgentes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-red-600">Mes Ventes Urgentes</h1>
        <a href="{{ route('prestataire.urgent-sales.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Ajouter une vente
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-tag text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total des ventes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Actives</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-eye text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Vues totales</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_views'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Contacts reçus</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_contacts'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('prestataire.urgent-sales.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par titre..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            
            <div>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Vendu</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            
            <div>
                <select name="condition" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Toutes les conditions</option>
                    <option value="new" {{ request('condition') === 'new' ? 'selected' : '' }}>Neuf</option>
                    <option value="like_new" {{ request('condition') === 'like_new' ? 'selected' : '' }}>Comme neuf</option>
                    <option value="good" {{ request('condition') === 'good' ? 'selected' : '' }}>Bon état</option>
                    <option value="fair" {{ request('condition') === 'fair' ? 'selected' : '' }}>État correct</option>
                    <option value="poor" {{ request('condition') === 'poor' ? 'selected' : '' }}>Mauvais état</option>
                </select>
            </div>
            
            <div>
                <select name="sort" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="created_at_desc" {{ request('sort') === 'created_at_desc' ? 'selected' : '' }}>Plus récent</option>
                    <option value="created_at_asc" {{ request('sort') === 'created_at_asc' ? 'selected' : '' }}>Plus ancien</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                    <option value="views_desc" {{ request('sort') === 'views_desc' ? 'selected' : '' }}>Plus vues</option>
                </select>
            </div>
            
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition duration-200">
                <i class="fas fa-search mr-2"></i>Filtrer
            </button>
            
            @if(request()->hasAny(['search', 'status', 'condition', 'sort']))
                <a href="{{ route('prestataire.urgent-sales.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
            @endif
        </form>
    </div>

    <!-- Liste des ventes -->
    @if($urgentSales->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($urgentSales as $sale)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition duration-200">
                    <!-- Image -->
                    <div class="relative">
                        @if($sale->photos && count($sale->photos) > 0)
                            <img src="{{ Storage::url($sale->photos[0]) }}" alt="{{ $sale->title }}" class="w-full h-48 object-cover rounded-t-lg">
                        @else
                            <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-3xl"></i>
                            </div>
                        @endif
                        
                        <!-- Badge urgent -->
                        @if($sale->is_urgent)
                            <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-bolt mr-1"></i>URGENT
                            </span>
                        @endif
                        
                        <!-- Badge statut -->
                        <span class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-semibold
                            @if($sale->status === 'active') bg-green-100 text-green-800
                            @elseif($sale->status === 'sold') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $sale->status_label }}
                        </span>
                    </div>
                    
                    <!-- Contenu -->
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 mb-2 line-clamp-2">{{ $sale->title }}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $sale->description }}</p>
                        
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-2xl font-bold text-red-600">{{ number_format($sale->price, 0, ',', ' ') }} €</span>
                            <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $sale->condition_label }}</span>
                        </div>
                        
                        <!-- Statistiques -->
                        <div class="flex justify-between text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-eye mr-1"></i>{{ $sale->views_count }} vues</span>
                            <span><i class="fas fa-envelope mr-1"></i>{{ $sale->contact_count }} contacts</span>
                            <span><i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($sale->location, 15) }}</span>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('prestataire.urgent-sales.show', $sale) }}" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-center py-2 rounded-md transition duration-200 text-sm">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                            
                            @if($sale->canBeEdited())
                                <a href="{{ route('prestataire.urgent-sales.edit', $sale) }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-2 rounded-md transition duration-200 text-sm">
                                    <i class="fas fa-edit mr-1"></i>Modifier
                                </a>
                            @endif
                            
                            @if($sale->contact_count > 0)
                                <a href="{{ route('prestataire.urgent-sales.contacts', $sale) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md transition duration-200 text-sm">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $urgentSales->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-tag text-red-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune vente urgente</h3>
            <p class="text-gray-600 mb-4">Vous n'avez pas encore créé de vente urgente.</p>
            <a href="{{ route('prestataire.urgent-sales.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>Créer ma première vente
            </a>
        </div>
    @endif
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
</style>
@endpush
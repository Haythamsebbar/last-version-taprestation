@extends('layouts.app')

@section('title', 'Mes Services')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-blue-600">Mes Services</h1>
        <a href="{{ route('prestataire.services.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Ajouter un service
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-concierge-bell text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total des services</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Services Réservables</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['reservable'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-book-open text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Réservations totales</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_bookings'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Réservations confirmées</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['confirmed_bookings'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('prestataire.services.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par titre..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <select name="category" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="reservable" {{ request('status') === 'reservable' ? 'selected' : '' }}>Réservable</option>
                    <option value="non-reservable" {{ request('status') === 'non-reservable' ? 'selected' : '' }}>Non réservable</option>
                </select>
            </div>
            
            <div>
                <select name="sort" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="created_at_desc" {{ request('sort') === 'created_at_desc' ? 'selected' : '' }}>Plus récent</option>
                    <option value="created_at_asc" {{ request('sort') === 'created_at_asc' ? 'selected' : '' }}>Plus ancien</option>
                    <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Titre (A-Z)</option>
                    <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Titre (Z-A)</option>
                </select>
            </div>
            
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                <i class="fas fa-search mr-2"></i>Filtrer
            </button>
            
            @if(request()->hasAny(['search', 'status', 'category', 'sort']))
                <a href="{{ route('prestataire.services.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
            @endif
        </form>
    </div>

    <!-- Liste des services -->
    @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($services as $service)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition duration-200 flex flex-col">
                    <!-- Image -->
                    <div class="relative">
                        @if($service->images->isNotEmpty())
                            <img src="{{ Storage::url($service->images->first()->image_path) }}" alt="{{ $service->title }}" class="w-full h-48 object-cover rounded-t-lg">
                        @else
                            <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-3xl"></i>
                            </div>
                        @endif
                        
                        <!-- Badge réservable -->
                        @if($service->reservable)
                            <span class="absolute top-2 left-2 bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-calendar-check mr-1"></i>Réservable
                            </span>
                        @endif
                    </div>
                    
                    <!-- Contenu -->
                    <div class="p-4 flex-grow">
                        <h3 class="font-semibold text-lg text-gray-900 mb-2 line-clamp-2">{{ $service->title }}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ $service->description }}</p>
                        
                        <div class="flex flex-wrap gap-2 mb-3">
                            @forelse($service->categories as $category)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $category->name }}</span>
                            @empty
                                <span class="text-xs text-gray-500 italic">Non catégorisé</span>
                            @endforelse
                        </div>

                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span><i class="fas fa-book-open mr-1"></i>{{ $service->bookings->count() }} réservations</span>
                            <span><i class="fas fa-tag mr-1"></i>{{ $service->price ? number_format($service->price, 2, ',', ' ') . ' €' : 'Prix sur devis' }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
                        <div class="flex gap-2">
                            <a href="{{ route('services.show', $service) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-md transition duration-200 text-sm">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                            <a href="{{ route('prestataire.services.edit', $service) }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-2 rounded-md transition duration-200 text-sm">
                                <i class="fas fa-edit mr-1"></i>Modifier
                            </a>
                            @if($service->reservable)
                                <a href="{{ route('prestataire.availabilities.index', $service) }}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-md transition duration-200 text-sm" title="Gérer les disponibilités">
                                    <i class="fas fa-calendar-alt"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $services->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-concierge-bell text-blue-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun service trouvé</h3>
            <p class="text-gray-600 mb-4">Affinez vos critères de recherche ou créez un nouveau service.</p>
            <a href="{{ route('prestataire.services.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>Créer un service
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
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
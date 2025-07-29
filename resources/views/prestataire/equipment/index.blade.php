@extends('layouts.app')

@section('title', 'Mes équipements à louer')

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .stat-icon {
        background: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-to));
    }
    .filter-container {
        background: rgba(248, 250, 252, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.5);
    }
    .empty-state {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .trend-indicator {
        font-size: 0.75rem;
        font-weight: 600;
    }
    @media (max-width: 768px) {
        .mobile-filters {
            display: none;
        }
        .mobile-filters.show {
            display: block;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête amélioré -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                            Matériel à louer
                        </h1>
                        <p class="text-gray-600 text-lg">Ajoutez vos équipements et gérez vos locations en temps réel.</p>
                    </div>
                </div>
                <div class="mt-6 lg:mt-0 flex items-center space-x-4">
                    <button class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Aide">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <a href="{{ route('prestataire.equipment.create') }}" 
                       class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter un équipement
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques améliorées -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-white rounded-xl shadow-lg p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 stat-icon from-blue-400 to-blue-600 rounded-full opacity-10 transform translate-x-6 -translate-y-6"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total équipements</p>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                            <span class="trend-indicator text-green-600">↗ +5%</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white shadow-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-lg p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 stat-icon from-green-400 to-green-600 rounded-full opacity-10 transform translate-x-6 -translate-y-6"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Disponibles</p>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['available'] ?? 0 }}</p>
                            <span class="trend-indicator text-green-600">↗ +2%</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white shadow-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-lg p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 stat-icon from-yellow-400 to-yellow-600 rounded-full opacity-10 transform translate-x-6 -translate-y-6"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">En location</p>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['rented'] ?? 0 }}</p>
                            <span class="trend-indicator text-yellow-600">→ 0%</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white shadow-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-lg p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 stat-icon from-purple-400 to-purple-600 rounded-full opacity-10 transform translate-x-6 -translate-y-6"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Revenus ce mois</p>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['monthly_revenue'] ?? 0, 0, ',', ' ') }}€</p>
                            <span class="trend-indicator text-green-600">↗ +12%</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section des demandes de location récentes -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Dernières demandes de location</h2>
            @if($rentalRequests->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <p class="text-gray-500">Aucune demande de location pour le moment.</p>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        @foreach($rentalRequests->take(5) as $request)
                            <li class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                <a href="{{ route('prestataire.equipment-rental-requests.show', $request) }}" class="block">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            @if($request->equipment->main_photo)
                                                <img src="{{ Storage::url($request->equipment->main_photo) }}" alt="{{ $request->equipment->name }}" class="w-12 h-12 rounded-lg object-cover mr-4">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $request->equipment->name }}</p>
                                                <p class="text-sm text-gray-600">Demandé par {{ $request->client->user->name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">{{ $request->created_at->diffForHumans() }}</p>
                                            @php
                                                $statusClass = '';
                                                switch ($request->status) {
                                                    case 'pending':
                                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                                        break;
                                                    case 'accepted':
                                                        $statusClass = 'bg-green-100 text-green-800';
                                                        break;
                                                    case 'rejected':
                                                        $statusClass = 'bg-red-100 text-red-800';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'bg-gray-100 text-gray-800';
                                                        break;
                                                    case 'expired':
                                                        $statusClass = 'bg-gray-100 text-gray-800';
                                                        break;
                                                }
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ $request->formatted_status }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @if($rentalRequests->count() > 5)
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <a href="{{ route('prestataire.bookings.index') }}" 
                               class="inline-flex items-center justify-center w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Voir toutes les demandes ({{ $rentalRequests->count() }})
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Zone de recherche et filtres améliorée -->
        <div class="filter-container rounded-xl shadow-lg p-6 mb-8">
            <!-- Bouton mobile pour afficher/masquer les filtres -->
            <div class="md:hidden mb-4">
                <button type="button" id="toggle-filters" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                    </svg>
                    Afficher les filtres
                </button>
            </div>
            
            <form method="GET" action="{{ route('prestataire.equipment.index') }}" class="mobile-filters">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-semibold text-gray-700 mb-3">🔍 Rechercher un équipement</label>
                        <div class="relative">
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                   placeholder="Tapez le nom de l'équipement..."
                                   class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-3">📂 Catégorie</label>
                        <select id="category" name="category" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">📊 Statut</label>
                        <select id="status" name="status" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                            <option value="">Tous les statuts</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>✅ Disponible</option>
                            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>🔄 En location</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>🔧 En maintenance</option>
                            <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>❌ Indisponible</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex flex-col sm:flex-row gap-4">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 inline-flex items-center justify-center shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Appliquer les filtres
                    </button>
                    <a href="{{ route('prestataire.equipment.index') }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des équipements -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($equipment->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix/jour</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($equipment as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if($item->main_photo)
                                                    <img class="h-12 w-12 rounded-lg object-cover" 
                                                         src="{{ Storage::url($item->main_photo) }}" 
                                                         alt="{{ $item->name }}">
                                                @else
                                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $item->categories->first()->name ?? 'Non catégorisé' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($item->daily_rate, 0, ',', ' ') }}€
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'available' => 'bg-green-100 text-green-800',
                                                'rented' => 'bg-yellow-100 text-yellow-800',
                                                'maintenance' => 'bg-red-100 text-red-800',
                                                'unavailable' => 'bg-gray-100 text-gray-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$item->availability_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $item->formatted_availability_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $item->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600">({{ $item->reviews_count }})</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('prestataire.equipment.show', $item) }}" 
                                               class="text-green-600 hover:text-green-900">Voir</a>
                                            <a href="{{ route('prestataire.equipment.edit', $item) }}" 
                                               class="text-green-600 hover:text-green-900">Modifier</a>
                                            <form method="POST" action="{{ route('prestataire.equipment.toggle-status', $item) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-{{ $item->is_active ? 'red' : 'green' }}-600 hover:text-{{ $item->is_active ? 'red' : 'green' }}-900">
                                                    {{ $item->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $equipment->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state rounded-2xl p-16 text-center relative overflow-hidden">
                    <!-- Illustration vectorielle -->
                    <div class="relative z-10">
                        <div class="mx-auto w-32 h-32 mb-8 relative">
                            <svg class="w-full h-full text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 9h.01M15 9h.01M9 15h.01M15 15h.01"></path>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-white mb-4">🏗️ Votre entrepôt est vide</h3>
                        <p class="text-lg text-white opacity-90 mb-8 max-w-md mx-auto">Commencez par ajouter votre premier équipement et transformez votre matériel en source de revenus !</p>
                        
                        <div class="space-y-4">
                            <a href="{{ route('prestataire.equipment.create') }}" 
                               class="inline-flex items-center px-8 py-4 bg-white text-green-600 font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Ajouter mon premier équipement
                            </a>
                            
                            <div class="mt-6">
                                <button class="text-white opacity-75 hover:opacity-100 underline text-sm transition-opacity duration-200">
                                    📖 Consulter un exemple de fiche d'équipement
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Éléments décoratifs -->
                    <div class="absolute top-0 left-0 w-full h-full">
                        <div class="absolute top-10 left-10 w-20 h-20 bg-white opacity-10 rounded-full"></div>
                        <div class="absolute bottom-10 right-10 w-16 h-16 bg-white opacity-10 rounded-full"></div>
                        <div class="absolute top-1/2 left-5 w-8 h-8 bg-white opacity-10 rounded-full"></div>
                        <div class="absolute top-1/4 right-5 w-12 h-12 bg-white opacity-10 rounded-full"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script pour le responsive design mobile
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggle-filters');
        const filtersContainer = document.querySelector('.mobile-filters');
        
        if (toggleButton && filtersContainer) {
            toggleButton.addEventListener('click', function() {
                filtersContainer.classList.toggle('show');
                const isShowing = filtersContainer.classList.contains('show');
                toggleButton.textContent = isShowing ? 'Masquer les filtres' : 'Afficher les filtres';
            });
        }
        
        // Animation des cartes statistiques au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.stat-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    });
</script>
@endpush

@endsection
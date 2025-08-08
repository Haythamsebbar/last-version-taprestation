@extends('layouts.app')

@section('title', 'Mes demandes de location')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl shadow-lg border border-blue-200 p-6 md:p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="text-center md:text-left">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Mes demandes de location</h1>
                    <p class="text-gray-600 text-base md:text-lg">Gérez et consultez l'ensemble de vos demandes de location de matériel</p>
                </div>
                <div class="flex justify-center md:justify-end">
                    <a href="{{ route('equipment.index') }}" class="bg-blue-600 text-white px-6 py-3 md:px-6 md:py-3 rounded-xl hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl border border-blue-700 font-semibold text-base md:text-lg flex items-center justify-center w-full md:w-auto" style="min-height: 44px;">
                        <i class="fas fa-plus mr-3"></i>
                        Nouvelle demande
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif
        
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 text-blue-600 shadow-lg">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gradient-to-br from-yellow-100 to-yellow-200 text-yellow-600 shadow-lg">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">En attente</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gradient-to-br from-green-100 to-green-200 text-green-600 shadow-lg">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Acceptées</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['accepted'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gradient-to-br from-red-100 to-red-200 text-red-600 shadow-lg">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">Refusées</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('client.equipment-rental-requests.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-search mr-2 text-blue-600"></i>Rechercher
                    </label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Nom de l'équipement, prestataire..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>
                
                <div class="md:w-48">
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-filter mr-2 text-blue-600"></i>Statut
                    </label>
                    <select id="status" 
                            name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Acceptée</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Refusée</option>
                    </select>
                </div>
                
                <div class="md:w-48">
                    <label for="date_from" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>Date de début
                    </label>
                    <input type="date" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>
                
                <div class="flex space-x-2">
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('client.equipment-rental-requests.index') }}" 
                       class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Liste des demandes -->
        @if($requests->count() > 0)
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Vue Desktop -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Équipement
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Prestataire
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Période
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Montant
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Date de demande
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-14 w-14">
                                        @if($request->equipment->photos && count($request->equipment->photos) > 0)
                                        <img class="h-14 w-14 rounded-xl object-cover shadow-md" 
                                             src="{{ Storage::url($request->equipment->photos[0]) }}" 
                                             alt="{{ $request->equipment->name }}">
                                        @else
                                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center shadow-md">
                                            <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $request->equipment->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $request->equipment->brand }} {{ $request->equipment->model }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $request->equipment->prestataire->company_name ?? $request->equipment->prestataire->first_name . ' ' . $request->equipment->prestataire->last_name }}
                                </div>
                                @if($request->equipment->prestataire->address)
                                <div class="text-sm text-gray-500">{{ $request->equipment->prestataire->address }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">
                                    Du {{ $request->start_date->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    au {{ $request->end_date->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    ({{ $request->duration_days }} jour{{ $request->duration_days > 1 ? 's' : '' }})
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ number_format($request->total_amount, 0) }}€
                                </div>
                                @if($request->delivery_required && $request->delivery_cost > 0)
                                <div class="text-xs text-gray-500">
                                    (+ {{ number_format($request->delivery_cost, 0) }}€ livraison)
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($request->status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 shadow-sm">
                                    <i class="fas fa-clock mr-1"></i>
                                    En attente
                                </span>
                                @elseif($request->status === 'accepted')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-green-100 to-green-200 text-green-800 shadow-sm">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Acceptée
                                </span>
                                @elseif($request->status === 'rejected')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-100 to-red-200 text-red-800 shadow-sm">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Refusée
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                {{ $request->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('client.equipment-rental-requests.show', $request) }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-semibold text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded-lg transition-all duration-200 hover:shadow-lg">
                                        <i class="fas fa-eye mr-1"></i>
                                        Voir
                                    </a>
                                    
                                    @if($request->status === 'pending')
                                    <form method="POST" 
                                          action="{{ route('client.equipment-rental-requests.destroy', $request) }}" 
                                          class="inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-2 text-sm font-semibold text-red-600 hover:text-white hover:bg-red-600 border border-red-600 rounded-lg transition-all duration-200 hover:shadow-lg">
                                            <i class="fas fa-times mr-1"></i>
                                            Annuler
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Vue Mobile et Tablette -->
            <div class="lg:hidden">
                <div class="space-y-4 p-4">
                    @foreach($requests as $request)
                    <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                        <!-- En-tête de la carte -->
                        <div class="flex items-start space-x-4 mb-4">
                            <div class="flex-shrink-0">
                                @if($request->equipment->photos && count($request->equipment->photos) > 0)
                                <img class="h-16 w-16 rounded-xl object-cover shadow-md" 
                                     src="{{ Storage::url($request->equipment->photos[0]) }}" 
                                     alt="{{ $request->equipment->name }}">
                                @else
                                <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center shadow-md">
                                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $request->equipment->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $request->equipment->brand }} {{ $request->equipment->model }}</p>
                                <div class="mt-2">
                                    @if($request->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 shadow-sm">
                                        <i class="fas fa-clock mr-1"></i>
                                        En attente
                                    </span>
                                    @elseif($request->status === 'accepted')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-green-100 to-green-200 text-green-800 shadow-sm">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Acceptée
                                    </span>
                                    @elseif($request->status === 'rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-100 to-red-200 text-red-800 shadow-sm">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Refusée
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations détaillées -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-building text-blue-600 mr-2"></i>
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Prestataire</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $request->equipment->prestataire->company_name ?? $request->equipment->prestataire->first_name . ' ' . $request->equipment->prestataire->last_name }}
                                </p>
                                @if($request->equipment->prestataire->address)
                                <p class="text-xs text-gray-500 mt-1">{{ $request->equipment->prestataire->address }}</p>
                                @endif
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Période</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">
                                    Du {{ $request->start_date->format('d/m/Y') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    au {{ $request->end_date->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    ({{ $request->duration_days }} jour{{ $request->duration_days > 1 ? 's' : '' }})
                                </p>
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-euro-sign text-blue-600 mr-2"></i>
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Montant</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ number_format($request->total_amount, 0) }}€
                                </p>
                                @if($request->delivery_required && $request->delivery_cost > 0)
                                <p class="text-xs text-gray-500">
                                    (+ {{ number_format($request->delivery_cost, 0) }}€ livraison)
                                </p>
                                @endif
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-clock text-blue-600 mr-2"></i>
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Demandé le</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $request->created_at->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    à {{ $request->created_at->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('client.equipment-rental-requests.show', $request) }}" 
                               class="flex-1 inline-flex items-center justify-center px-4 py-3 text-sm font-semibold text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded-lg transition-all duration-200 hover:shadow-lg">
                                <i class="fas fa-eye mr-2"></i>
                                Voir les détails
                            </a>
                            
                            @if($request->status === 'pending')
                            <form method="POST" 
                                  action="{{ route('client.equipment-rental-requests.destroy', $request) }}" 
                                  class="flex-1"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-3 text-sm font-semibold text-red-600 hover:text-white hover:bg-red-600 border border-red-600 rounded-lg transition-all duration-200 hover:shadow-lg">
                                    <i class="fas fa-times mr-2"></i>
                                    Annuler la demande
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Pagination -->
            @if($requests->hasPages())
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-t border-gray-200">
                {{ $requests->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
        @else
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="mx-auto h-24 w-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-clipboard-list text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Aucune demande trouvée</h3>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    @if(request()->hasAny(['search', 'status', 'date_from']))
                        Aucune demande ne correspond à vos critères de recherche. Essayez de modifier vos filtres.
                    @else
                        Vous n'avez pas encore fait de demande de location d'équipement. Commencez dès maintenant !
                    @endif
                </p>
                <div class="space-y-3">
                    <a href="{{ route('equipment.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-search mr-2"></i>
                        Parcourir les équipements
                    </a>
                    @if(request()->hasAny(['search', 'status', 'date_from']))
                    <div>
                        <a href="{{ route('client.equipment-rental-requests.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                            <i class="fas fa-redo mr-2"></i>
                            Réinitialiser les filtres
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
</div>
@endsection
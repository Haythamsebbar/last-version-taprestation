@extends('layouts.app')

@section('title', 'Gestion des locations')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des locations</h1>
            <p class="text-gray-600 mt-2">Suivez et gérez vos locations d'équipement en cours</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total locations</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En cours</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">À démarrer</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_start'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Revenus ce mois</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['monthly_revenue'] ?? 0, 0) }}€</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('prestataire.equipment-rentals.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Rechercher par client, équipement..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div class="min-w-48">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tous les statuts</option>
                    <option value="pending_start" {{ request('status') === 'pending_start' ? 'selected' : '' }}>À démarrer</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>En cours</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminées</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulées</option>
                </select>
            </div>
            
            <div class="min-w-48">
                <select name="equipment_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tous les équipements</option>
                    @foreach($equipments as $equipment)
                    <option value="{{ $equipment->id }}" {{ request('equipment_id') == $equipment->id ? 'selected' : '' }}>
                        {{ $equipment->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                🔍 Filtrer
            </button>
            
            @if(request()->hasAny(['search', 'status', 'equipment_id']))
            <a href="{{ route('prestataire.equipment-rentals.index') }}" 
               class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200">
                ✖️ Effacer
            </a>
            @endif
        </form>
    </div>

    <!-- Liste des locations -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($rentals->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rentals as $rental)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium text-sm">
                                        {{ substr($rental->client->first_name, 0, 1) }}{{ substr($rental->client->last_name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $rental->client->first_name }} {{ $rental->client->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $rental->client->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($rental->equipment->main_photo)
                                <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    <img src="{{ Storage::url($rental->equipment->main_photo) }}" 
                                         alt="{{ $rental->equipment->name }}"
                                         class="w-full h-full object-cover">
                                </div>
                                @endif
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $rental->equipment->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $rental->equipment->brand }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>
                                <div class="font-medium">{{ $rental->start_date->format('d/m/Y') }}</div>
                                <div class="text-gray-500">au {{ $rental->end_date->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400">
                                    {{ $rental->start_date->diffInDays($rental->end_date) + 1 }} jour(s)
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium">{{ number_format($rental->total_amount, 2) }}€</div>
                            @if($rental->deposit_amount > 0)
                            <div class="text-xs text-gray-500">Caution: {{ number_format($rental->deposit_amount, 2) }}€</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($rental->status === 'pending_start') bg-yellow-100 text-yellow-800
                                @elseif($rental->status === 'active') bg-green-100 text-green-800
                                @elseif($rental->status === 'completed') bg-blue-100 text-blue-800
                                @elseif($rental->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($rental->status === 'pending_start') À démarrer
                                @elseif($rental->status === 'active') En cours
                                @elseif($rental->status === 'completed') Terminée
                                @elseif($rental->status === 'cancelled') Annulée
                                @else {{ ucfirst($rental->status) }} @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('prestataire.equipment-rentals.show', $rental) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                   title="Voir les détails">
                                    👁️
                                </a>
                                
                                @if($rental->status === 'pending_start')
                                <form method="POST" action="{{ route('prestataire.equipment-rentals.start', $rental) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                            title="Démarrer la location"
                                            onclick="return confirm('Démarrer cette location ?')">
                                        ▶️
                                    </button>
                                </form>
                                @endif
                                
                                @if($rental->status === 'active')
                                <form method="POST" action="{{ route('prestataire.equipment-rentals.complete', $rental) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                            title="Marquer comme terminée"
                                            onclick="return confirm('Marquer cette location comme terminée ?')">
                                        ✅
                                    </button>
                                </form>
                                @endif
                                
                                @if(in_array($rental->status, ['pending_start', 'active']))
                                <button type="button" 
                                        class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                        title="Signaler un problème"
                                        onclick="reportProblem({{ $rental->id }})">
                                    ⚠️
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($rentals->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $rentals->links() }}
        </div>
        @endif
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune location</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'status', 'equipment_id']))
                    Aucune location ne correspond à vos critères de recherche.
                @else
                    Vous n'avez pas encore de location d'équipement.
                @endif
            </p>
            @if(request()->hasAny(['search', 'status', 'equipment_id']))
            <div class="mt-6">
                <a href="{{ route('prestataire.equipment-rentals.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Voir toutes les locations
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Modal pour signaler un problème -->
<div id="problemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Signaler un problème</h3>
            <form id="problemForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="problem_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description du problème *
                    </label>
                    <textarea id="problem_description" 
                              name="problem_description" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Décrivez le problème rencontré..."></textarea>
                </div>
                
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="closeProblemModal()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition-colors duration-200">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Signaler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function reportProblem(rentalId) {
    const modal = document.getElementById('problemModal');
    const form = document.getElementById('problemForm');
    
    form.action = `/prestataire/equipment-rentals/${rentalId}/report-problem`;
    modal.classList.remove('hidden');
}

function closeProblemModal() {
    const modal = document.getElementById('problemModal');
    const form = document.getElementById('problemForm');
    
    modal.classList.add('hidden');
    form.reset();
}

// Fermer la modal en cliquant à l'extérieur
document.getElementById('problemModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeProblemModal();
    }
});
</script>
@endsection
@extends('layouts.app')

@section('title', $savedSearch->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-start mb-8">
        <div class="flex-1">
            <div class="flex items-center mb-4">
                <a href="{{ route('saved-searches.index') }}" class="text-blue-600 hover:text-blue-700 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $savedSearch->name }}</h1>
                <span class="ml-4 px-3 py-1 rounded-full text-sm font-medium {{ $savedSearch->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $savedSearch->is_active ? 'Actif' : 'Inactif' }}
                </span>
            </div>
            <div class="flex items-center space-x-6 text-sm text-gray-600">
                <span class="flex items-center">
                    <i class="fas fa-calendar mr-2"></i>
                    Créée le {{ $savedSearch->created_at->format('d/m/Y à H:i') }}
                </span>
                <span class="flex items-center">
                    <i class="fas fa-bell mr-2"></i>
                    Alertes {{ $savedSearch->alert_frequency_name }}
                </span>
                <span class="flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    {{ $savedSearch->matchingAlerts->count() }} correspondances trouvées
                </span>
                @if($savedSearch->last_alert_sent_at)
                    <span class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        Dernière alerte : {{ $savedSearch->last_alert_sent_at->diffForHumans() }}
                    </span>
                @endif
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="runSearch()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-sync mr-2"></i>
                Actualiser
            </button>
            <button onclick="toggleAlerts()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-{{ $savedSearch->is_active ? 'bell-slash' : 'bell' }} mr-2"></i>
                {{ $savedSearch->is_active ? 'Désactiver' : 'Activer' }}
            </button>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                    <div class="py-1">
                        <button onclick="editSearch()" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </button>
                        <a href="{{ route('matching-alerts.index', ['saved_search_id' => $savedSearch->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-bell mr-2"></i>Voir les alertes
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <button onclick="deleteSearch()" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-trash mr-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Critères de recherche -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Critères de recherche</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($savedSearch->formatted_criteria as $key => $value)
                <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-{{ $this->getCriteriaIcon($key) }} text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $key }}</p>
                        <p class="text-sm text-gray-600">{{ $value }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="{{ $savedSearch->search_url }}" class="text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-external-link-alt mr-2"></i>
                Modifier les critères de recherche
            </a>
        </div>
    </div>

    <!-- Statistiques des correspondances -->
    @if($savedSearch->matchingAlerts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $savedSearch->matchingAlerts->count() }}</p>
                        <p class="text-sm text-gray-600">Total correspondances</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $savedSearch->matchingAlerts->where('matching_score', '>=', 0.8)->count() }}</p>
                        <p class="text-sm text-gray-600">Correspondances élevées</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $savedSearch->matchingAlerts->where('created_at', '>=', now()->subWeek())->count() }}</p>
                        <p class="text-sm text-gray-600">Cette semaine</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($savedSearch->matchingAlerts->avg('matching_score') * 100, 1) }}%</p>
                        <p class="text-sm text-gray-600">Score moyen</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Résultats de recherche -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Résultats de recherche</h2>
                <div class="flex items-center space-x-4">
                    <select id="sortBy" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="relevance">Pertinence</option>
                        <option value="rating">Note</option>
                        <option value="distance">Distance</option>
                        <option value="recent">Plus récent</option>
                    </select>
                    <div class="flex items-center space-x-2">
                        <button id="gridView" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button id="listView" class="p-2 text-blue-600">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if(isset($results) && $results->count() > 0)
                <div id="resultsContainer" class="space-y-6">
                    @foreach($results as $prestataire)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start space-x-4">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    @if($prestataire->avatar_url)
                                        <img src="{{ $prestataire->avatar_url }}" alt="{{ $prestataire->user->name }}" class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <i class="fas fa-user text-gray-400 text-xl"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('prestataires.show', $prestataire) }}" class="hover:text-blue-600 transition-colors">
                                                    {{ $prestataire->user->name }}
                                                </a>
                                            </h3>
                                            @if($prestataire->company_name)
                                                <p class="text-gray-600">{{ $prestataire->company_name }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            @if($prestataire->average_rating)
                                                <div class="flex items-center mb-1">
                                                    <div class="flex text-yellow-400 mr-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $prestataire->average_rating ? '' : 'text-gray-300' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <span class="text-sm text-gray-600">({{ $prestataire->reviews_count }})</span>
                                                </div>
                                            @endif
                                            @if(isset($prestataire->distance))
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ number_format($prestataire->distance, 1) }} km
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($prestataire->description)
                                        <p class="text-gray-600 mb-3">{{ Str::limit($prestataire->description, 150) }}</p>
                                    @endif
                                    
                                    @if($prestataire->services->count() > 0)
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            @foreach($prestataire->services->take(3) as $service)
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                                    {{ $service->name }}
                                                </span>
                                            @endforeach
                                            @if($prestataire->services->count() > 3)
                                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                                                    +{{ $prestataire->services->count() - 3 }} autres
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            @if($prestataire->city)
                                                <span>
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $prestataire->city }}
                                                </span>
                                            @endif
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                Actif {{ $prestataire->user->last_seen_at ? $prestataire->user->last_seen_at->diffForHumans() : 'récemment' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('prestataires.show', $prestataire) }}" class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                                                Voir le profil
                                            </a>
                                            <a href="{{ route('messaging.show', $prestataire->user) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                Contacter
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Aucun résultat trouvé</h3>
                    <p class="text-gray-600 mb-6">Aucun prestataire ne correspond actuellement à vos critères de recherche</p>
                    <button onclick="runSearch()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Actualiser la recherche
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function runSearch() {
    fetch(`/saved-searches/{{ $savedSearch->id }}/run`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de l\'actualisation de la recherche');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'actualisation de la recherche');
    });
}

function toggleAlerts() {
    fetch(`/saved-searches/{{ $savedSearch->id }}/toggle-alerts`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification des alertes');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la modification des alertes');
    });
}

function editSearch() {
    // Rediriger vers la page de recherche avec les critères pré-remplis
    window.location.href = `{{ $savedSearch->search_url }}`;
}

function deleteSearch() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette recherche ? Cette action est irréversible.')) {
        fetch(`/saved-searches/{{ $savedSearch->id }}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/saved-searches';
            } else {
                alert('Erreur lors de la suppression de la recherche');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression de la recherche');
        });
    }
}

// Gestion des vues (grille/liste)
document.getElementById('gridView').addEventListener('click', function() {
    // Implémenter la vue en grille
    this.classList.add('text-blue-600');
    this.classList.remove('text-gray-400');
    document.getElementById('listView').classList.add('text-gray-400');
    document.getElementById('listView').classList.remove('text-blue-600');
});

document.getElementById('listView').addEventListener('click', function() {
    // Implémenter la vue en liste
    this.classList.add('text-blue-600');
    this.classList.remove('text-gray-400');
    document.getElementById('gridView').classList.add('text-gray-400');
    document.getElementById('gridView').classList.remove('text-blue-600');
});

// Gestion du tri
document.getElementById('sortBy').addEventListener('change', function() {
    // Implémenter le tri des résultats
    const sortBy = this.value;
    // Ici vous pourriez faire un appel AJAX pour retrier les résultats
});
</script>
@endpush

@php
function getCriteriaIcon($key) {
    $icons = [
        'Service' => 'cogs',
        'Ville' => 'map-marker-alt',
        'Région' => 'map',
        'Note minimale' => 'star',
        'Budget maximum' => 'euro-sign',
        'Distance' => 'route',
        'Disponibilité' => 'calendar',
        'Expérience' => 'medal'
    ];
    
    return $icons[$key] ?? 'tag';
}
@endphp
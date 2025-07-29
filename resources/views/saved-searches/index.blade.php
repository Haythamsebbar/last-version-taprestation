@extends('layouts.app')

@section('title', 'Mes Recherches Sauvegardées')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mes Recherches Sauvegardées</h1>
            <p class="text-gray-600 mt-2">Gérez vos recherches et recevez des alertes pour de nouveaux prestataires</p>
        </div>
        <!-- Bouton "Nouvelle Recherche" supprimé -->
    </div>

    @if($savedSearches->count() > 0)
        <div class="grid gap-6">
            @foreach($savedSearches as $search)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <a href="{{ route('saved-searches.show', $search) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $search->name }}
                                </a>
                            </h3>
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Créée le {{ $search->created_at->format('d/m/Y') }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-bell mr-1"></i>
                                    Alertes {{ $search->alert_frequency_name }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    {{ $search->matchingAlerts->count() }} correspondances
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $search->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $search->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                    <div class="py-1">
                                        <a href="{{ route('saved-searches.show', $search) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-eye mr-2"></i>Voir les résultats
                                        </a>
                                        <button onclick="runSearch({{ $search->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-play mr-2"></i>Exécuter maintenant
                                        </button>
                                        <button onclick="toggleAlerts({{ $search->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-{{ $search->is_active ? 'bell-slash' : 'bell' }} mr-2"></i>
                                            {{ $search->is_active ? 'Désactiver' : 'Activer' }} les alertes
                                        </button>
                                        <button onclick="editSearch({{ $search->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-edit mr-2"></i>Modifier
                                        </button>
                                        <div class="border-t border-gray-100"></div>
                                        <button onclick="deleteSearch({{ $search->id }})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="fas fa-trash mr-2"></i>Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Critères de recherche -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Critères de recherche :</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($search->formatted_criteria as $key => $value)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    {{ $key }} : {{ $value }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Alertes récentes -->
                    @if($search->matchingAlerts->count() > 0)
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Correspondances récentes :</h4>
                            <div class="space-y-2">
                                @foreach($search->matchingAlerts->take(3) as $alert)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $alert->prestataire->user->name ?? 'N/A' }}</p>
                                                <p class="text-sm text-gray-600">Score : {{ $alert->formatted_score }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $alert->match_level_color }}">
                                                {{ $alert->match_level_name }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ $alert->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endforeach
                                @if($search->matchingAlerts->count() > 3)
                                    <div class="text-center">
                                        <a href="{{ route('saved-searches.show', $search) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                            Voir toutes les correspondances ({{ $search->matchingAlerts->count() }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-gray-500 text-sm italic">Aucune correspondance trouvée pour le moment</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $savedSearches->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-search text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Aucune recherche sauvegardée</h3>
            <p class="text-gray-600 mb-6">Commencez par créer votre première recherche pour recevoir des alertes personnalisées</p>
            <!-- Bouton "Créer ma première recherche" supprimé -->
        </div>
    @endif
</div>

<!-- Modal d'édition -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4">Modifier la recherche</h3>
            <form id="editForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la recherche</label>
                    <input type="text" id="editName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fréquence des alertes</label>
                    <select id="editFrequency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="daily">Quotidienne</option>
                        <option value="weekly">Hebdomadaire</option>
                        <option value="monthly">Mensuelle</option>
                        <option value="never">Jamais</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                        Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSearchId = null;

function runSearch(searchId) {
    fetch(`/saved-searches/${searchId}/run`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.search_url || `/saved-searches/${searchId}`;
        } else {
            alert('Erreur lors de l\'exécution de la recherche');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'exécution de la recherche');
    });
}

function toggleAlerts(searchId) {
    fetch(`/saved-searches/${searchId}/toggle-alerts`, {
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

function editSearch(searchId) {
    currentSearchId = searchId;
    // Ici, vous pourriez charger les données actuelles de la recherche
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    currentSearchId = null;
}

function deleteSearch(searchId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette recherche ? Cette action est irréversible.')) {
        fetch(`/saved-searches/${searchId}`, {
            method: 'DELETE',
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
                alert('Erreur lors de la suppression de la recherche');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression de la recherche');
        });
    }
}

// Gestion du formulaire d'édition
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentSearchId) return;
    
    const formData = {
        name: document.getElementById('editName').value,
        alert_frequency: document.getElementById('editFrequency').value
    };
    
    fetch(`/saved-searches/${currentSearchId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour de la recherche');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la mise à jour de la recherche');
    });
});
</script>
@endpush
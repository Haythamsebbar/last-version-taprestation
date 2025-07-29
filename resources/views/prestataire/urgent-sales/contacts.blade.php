@extends('layouts.app')

@section('title', 'Contacts - ' . $urgentSale->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <a href="{{ route('prestataire.urgent-sales.show', $urgentSale) }}" class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Contacts reçus</h1>
                    <p class="text-gray-600 mt-1">{{ $urgentSale->title }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                    {{ $contacts->total() }} contact{{ $contacts->total() > 1 ? 's' : '' }}
                </span>
                
                <a href="{{ route('prestataire.urgent-sales.show', $urgentSale) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-eye mr-2"></i>Voir la vente
                </a>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total contacts</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $contacts->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En attente</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Répondus</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $respondedCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-calendar text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Aujourd'hui</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $todayCount }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('prestataire.urgent-sales.contacts', $urgentSale) }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou message..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="responded" {{ request('status') === 'responded' ? 'selected' : '' }}>Répondu</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fermé</option>
                        </select>
                    </div>
                    
                    <div>
                        <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Plus récents</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Plus anciens</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                    
                    @if(request()->hasAny(['search', 'status', 'sort']))
                        <a href="{{ route('prestataire.urgent-sales.contacts', $urgentSale) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-times mr-2"></i>Réinitialiser
                        </a>
                    @endif
                </form>
            </div>
        </div>
        
        <!-- Liste des contacts -->
        @if($contacts->count() > 0)
            <div class="space-y-4">
                @foreach($contacts as $contact)
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $contact->user->name }}</h3>
                                        <p class="text-gray-600 text-sm">{{ $contact->created_at->format('d/m/Y à H:i') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                                        @if($contact->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($contact->status === 'responded') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $contact->status_label }}
                                    </span>
                                    
                                    @if($contact->status === 'pending')
                                        <button onclick="openResponseModal({{ $contact->id }}, '{{ $contact->user->name }}')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                                            <i class="fas fa-reply mr-1"></i>Répondre
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Message du contact -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="font-medium text-gray-900 mb-2">Message :</h4>
                                <p class="text-gray-700">{{ $contact->message }}</p>
                            </div>
                            
                            <!-- Informations de contact -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                @if($contact->phone)
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                                        <span class="text-gray-700">{{ $contact->phone }}</span>
                                        <a href="tel:{{ $contact->phone }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-external-link-alt text-sm"></i>
                                        </a>
                                    </div>
                                @endif
                                
                                @if($contact->email)
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        <span class="text-gray-700">{{ $contact->email }}</span>
                                        <a href="mailto:{{ $contact->email }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-external-link-alt text-sm"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Réponse (si elle existe) -->
                            @if($contact->response)
                                <div class="border-t pt-4">
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="font-medium text-blue-900">Votre réponse :</h4>
                                            <span class="text-blue-600 text-sm">{{ $contact->responded_at->format('d/m/Y à H:i') }}</span>
                                        </div>
                                        <p class="text-blue-800">{{ $contact->response }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $contacts->appends(request()->query())->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun contact trouvé</h3>
                <p class="text-gray-600">
                    @if(request()->hasAny(['search', 'status']))
                        Aucun contact ne correspond à vos critères de recherche.
                    @else
                        Vous n'avez pas encore reçu de contact pour cette vente.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('prestataire.urgent-sales.contacts', $urgentSale) }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        Voir tous les contacts
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal de réponse -->
<div id="response-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form id="response-form" method="POST">
                @csrf
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Répondre à <span id="contact-name"></span></h3>
                        <button type="button" onclick="closeResponseModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <label for="response" class="block text-sm font-medium text-gray-700 mb-2">
                            Votre réponse <span class="text-red-500">*</span>
                        </label>
                        <textarea id="response" name="response" required rows="4" maxlength="1000" placeholder="Tapez votre réponse..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-gray-500 text-sm">Cette réponse sera envoyée par email au client.</span>
                            <span id="response-count" class="text-sm text-gray-500">0/1000</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" onclick="closeResponseModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentContactId = null;

// Ouvrir le modal de réponse
function openResponseModal(contactId, contactName) {
    currentContactId = contactId;
    document.getElementById('contact-name').textContent = contactName;
    document.getElementById('response-form').action = `{{ route('prestataire.urgent-sales.contacts', $urgentSale) }}/${contactId}/respond`;
    document.getElementById('response').value = '';
    updateResponseCount();
    document.getElementById('response-modal').classList.remove('hidden');
    document.getElementById('response').focus();
}

// Fermer le modal de réponse
function closeResponseModal() {
    document.getElementById('response-modal').classList.add('hidden');
    currentContactId = null;
}

// Compteur de caractères pour la réponse
const responseTextarea = document.getElementById('response');
const responseCount = document.getElementById('response-count');

function updateResponseCount() {
    const count = responseTextarea.value.length;
    responseCount.textContent = `${count}/1000`;
    
    if (count > 900) {
        responseCount.classList.add('text-red-500');
        responseCount.classList.remove('text-gray-500');
    } else {
        responseCount.classList.add('text-gray-500');
        responseCount.classList.remove('text-red-500');
    }
}

responseTextarea.addEventListener('input', updateResponseCount);

// Fermer le modal avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('response-modal').classList.contains('hidden')) {
        closeResponseModal();
    }
});

// Gestion du formulaire de réponse
document.getElementById('response-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi...';
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger la page pour afficher la réponse
            window.location.reload();
        } else {
            alert('Erreur lors de l\'envoi de la réponse. Veuillez réessayer.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'envoi de la réponse. Veuillez réessayer.');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});
</script>
@endpush
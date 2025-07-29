@extends('layouts.app')

@section('content')
<div class="bg-blue-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-extrabold text-blue-900 mb-2">Détails de la réservation</h1>
                <p class="text-lg text-blue-700">Numéro: {{ $booking->booking_number }}</p>
            </div>
            
            <div class="flex justify-center mb-8">
                <a href="{{ route('bookings.index') }}" 
                   class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold px-6 py-3 rounded-lg text-center transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Retour aux réservations
                </a>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Statut et actions -->
                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-blue-800 mb-5 border-b-2 border-blue-200 pb-3">Statut de la réservation</h2>
                            <span class="status-badge
                                @if($booking->status === 'pending') pending
                                @elseif($booking->status === 'confirmed') confirmed
                                @elseif($booking->status === 'completed') completed
                                @elseif($booking->status === 'cancelled') cancelled
                                @elseif($booking->status === 'refused') refused
                                @endif">
                                @if($booking->status === 'pending') 
                                    <i class="fas fa-clock"></i> En attente de confirmation
                                @elseif($booking->status === 'confirmed') 
                                    <i class="fas fa-check-circle"></i> Confirmée
                                @elseif($booking->status === 'completed') 
                                    <i class="fas fa-check-double"></i> Terminée
                                @elseif($booking->status === 'cancelled') 
                                    <i class="fas fa-times-circle"></i> Annulée
                                @elseif($booking->status === 'refused') 
                                    <i class="fas fa-ban"></i> Refusée
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex gap-2">
                            @if(auth()->user()->role === 'prestataire')
                                @if($booking->status === 'pending')
                                    <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-green-600 hover:bg-green-700 text-white font-bold px-4 py-2 rounded-lg transition duration-200">
                                            <i class="fas fa-check"></i> Confirmer
                                        </button>
                                    </form>
                                    <button onclick="openRefuseModal()" 
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg transition duration-200">
                                        <i class="fas fa-ban"></i> Refuser
                                    </button>
                                @elseif($booking->status === 'confirmed')
                                    <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg transition duration-200">
                                            <i class="fas fa-check-double"></i> Marquer comme terminé
                                        </button>
                                    </form>
                                @endif
                            @endif
                            
                            @if(auth()->user()->role === 'client' && in_array($booking->status, ['pending', 'confirmed']))
                                <button onclick="openCancelModal()" 
                                        class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg transition duration-200">
                                    <i class="fas fa-times"></i> Annuler la réservation
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    @if($booking->status === 'confirmed' && $booking->confirmed_at)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-blue-800 font-medium">Réservation confirmée le {{ $booking->confirmed_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>
                    @elseif($booking->status === 'completed' && $booking->completed_at)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-green-800 font-medium">Service terminé le {{ $booking->completed_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>
                    @elseif($booking->status === 'cancelled' || $booking->status === 'refused')
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-red-800 font-medium">
                                    @if($booking->status === 'cancelled')
                                        Réservation annulée le {{ $booking->cancelled_at->format('d/m/Y à H:i') }}
                                    @else
                                        Réservation refusée le {{ $booking->cancelled_at->format('d/m/Y à H:i') }}
                                    @endif
                                </span>
                            </div>
                            @if($booking->cancellation_reason)
                                <p class="text-red-700 text-sm"><strong>Raison:</strong> {{ $booking->cancellation_reason }}</p>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Détails de la réservation -->
                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-8">
                    <h2 class="text-2xl font-bold text-blue-800 mb-6 border-b-2 border-blue-200 pb-3">Détails de la réservation</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Informations générales</h3>
                            <div class="space-y-0">
                                <div class="booking-info-item">
                                    <div class="booking-info-label">
                                        <i class="fas fa-calendar-day"></i>
                                        <span>Date et heure:</span>
                                    </div>
                                    <div class="booking-info-value">{{ $booking->start_datetime->format('d/m/Y à H:i') }}</div>
                                </div>
                                @if($booking->end_datetime)
                                    <div class="booking-info-item">
                                        <div class="booking-info-label">
                                            <i class="fas fa-hourglass-end"></i>
                                            <span>Fin prévue:</span>
                                        </div>
                                        <div class="booking-info-value">{{ $booking->end_datetime->format('H:i') }}</div>
                                    </div>
                                @endif
                                <div class="booking-info-item">
                                    <div class="booking-info-label">
                                        <i class="fas fa-euro-sign"></i>
                                        <span>Prix total:</span>
                                    </div>
                                    <div class="booking-price">{{ number_format($booking->total_price, 2) }} €</div>
                                </div>
                                <div class="booking-info-item">
                                    <div class="booking-info-label">
                                        <i class="fas fa-clock"></i>
                                        <span>Créée le:</span>
                                    </div>
                                    <div class="booking-info-value">{{ $booking->created_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Service</h3>
                            <div class="space-y-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div>
                                    <h4 class="font-medium text-gray-900 flex items-center">
                                        <i class="fas fa-briefcase text-blue-500 mr-2"></i>
                                        {{ $booking->service->name }}
                                    </h4>
                                    <p class="text-gray-600 text-sm mt-2 pl-6">{{ $booking->service->description }}</p>
                                </div>
                                @if($booking->service->duration)
                                    <div class="text-sm text-gray-700 flex items-center">
                                        <i class="fas fa-clock text-blue-500 mr-2"></i>
                                        <span><strong>Durée:</strong> {{ $booking->service->duration }} minutes</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notes -->
                @if($booking->client_notes || $booking->prestataire_notes)
                    <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-8">
                        <h2 class="text-2xl font-bold text-blue-800 mb-6 border-b-2 border-blue-200 pb-3">Notes et commentaires</h2>
                        
                        @if($booking->client_notes)
                            <div class="mb-5">
                                <h3 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                    Notes du client
                                </h3>
                                <div class="notes-container notes-client">
                                    <p class="text-gray-700">{{ $booking->client_notes }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($booking->prestataire_notes)
                            <div>
                                <h3 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-comment-dots text-blue-500 mr-2"></i>
                                    Notes du prestataire
                                </h3>
                                <div class="notes-container notes-prestataire">
                                    <p class="text-blue-700">{{ $booking->prestataire_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6 sticky top-8">
                    @if(auth()->user()->role === 'client')
                        <h2 class="text-2xl font-bold text-blue-800 mb-5 border-b-2 border-blue-200 pb-3 flex items-center">
                            <i class="fas fa-user-tie text-blue-500 mr-2"></i> Prestataire
                        </h2>
                        <div class="flex items-center gap-4 mb-5">
                            <div class="relative">
                                @if($booking->prestataire->photo)
                                    <img src="{{ asset('storage/' . $booking->prestataire->photo) }}" 
                                         alt="{{ $booking->prestataire->user->name }}" 
                                         class="profile-photo">
                                @elseif($booking->prestataire->user->avatar)
                                    <img src="{{ asset('storage/' . $booking->prestataire->user->avatar) }}" 
                                         alt="{{ $booking->prestataire->user->name }}" 
                                         class="profile-photo">
                                @else
                                    <div class="profile-initial">
                                        <span>{{ substr($booking->prestataire->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                @if($booking->prestataire->isVerified())
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <div class="profile-name">{{ $booking->prestataire->user->name }}</div>
                                    @if($booking->prestataire->isVerified())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Vérifié
                                        </span>
                                    @endif
                                </div>
                                @if($booking->prestataire->location)
                                    <div class="profile-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $booking->prestataire->location }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <a href="{{ route('prestataires.show', $booking->prestataire) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition duration-200 w-full flex justify-center mb-3">
                                <i class="fas fa-id-card mr-2"></i> Voir le profil
                            </a>
                            <a href="{{ route('messaging.conversation', $booking->prestataire->user) }}" 
                               class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold px-6 py-3 rounded-lg transition duration-200 w-full flex justify-center">
                                <i class="fas fa-comment mr-2"></i> Envoyer un message
                            </a>
                        </div>
                    @else
                        <h2 class="text-2xl font-bold text-blue-800 mb-5 border-b-2 border-blue-200 pb-3 flex items-center">
                            <i class="fas fa-user text-blue-500 mr-2"></i> Client
                        </h2>
                        <div class="flex items-center gap-4 mb-5">
                            @if($booking->client->user->profile_photo)
                                <img src="{{ asset('storage/' . $booking->client->user->profile_photo) }}" 
                                     alt="{{ $booking->client->user->name }}" 
                                     class="profile-photo">
                            @elseif($booking->client->user->avatar)
                                <img src="{{ asset('storage/' . $booking->client->user->avatar) }}" 
                                     alt="{{ $booking->client->user->name }}" 
                                     class="profile-photo">
                            @else
                                <div class="profile-initial">
                                    <span>{{ substr($booking->client->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <div class="profile-name">{{ $booking->client->user->name }}</div>
                                <div class="profile-location">
                                    <i class="fas fa-user-tag"></i>
                                    Client
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <a href="{{ route('messaging.conversation', $booking->client->user) }}" 
                               class="btn-action primary w-full flex justify-center">
                                <i class="fas fa-comment"></i> Envoyer un message
                            </a>
                        </div>
                    @endif
                    
                    @if($booking->status === 'completed' && auth()->user()->role === 'client')
                        <div class="border-t pt-5 mt-5">
                            <a href="{{ route('reviews.create', ['prestataire' => $booking->prestataire->id, 'booking' => $booking->id]) }}" 
                               class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg text-center transition duration-200 block flex items-center justify-center font-medium">
                                <i class="fas fa-star mr-2"></i> Laisser un avis
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'annulation -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 modal-backdrop">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6 modal-container">
            <div class="modal-header flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3 text-xl"></i>
                <h3 class="text-lg font-semibold text-gray-900">Annuler la réservation</h3>
            </div>
            <form action="{{ route('bookings.cancel', $booking) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Raison de l'annulation *
                    </label>
                    <textarea id="cancellation_reason" name="cancellation_reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Veuillez expliquer la raison de l'annulation..."></textarea>
                    <p class="text-sm text-gray-500 mt-1">Cette information sera visible par l'autre partie.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeCancelModal()" 
                            class="btn-action secondary">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" 
                            class="btn-action danger">
                        <i class="fas fa-check"></i> Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancellation_reason').value = '';
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
@push('scripts')
<script>
    function openCancelModal() {
        document.getElementById('cancelModal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    function openRefuseModal() {
        document.getElementById('refuseModal').classList.remove('hidden');
    }

    function closeRefuseModal() {
        document.getElementById('refuseModal').classList.add('hidden');
    }
</script>
@endpush

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('bookings.cancel', $booking) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Annuler la réservation
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.
                                </p>
                                <div class="mt-4">
                                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">Raison de l'annulation (optionnel)</label>
                                    <textarea name="cancellation_reason" id="cancellation_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmer l'annulation
                    </button>
                    <button type="button" onclick="closeCancelModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Retour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Refuse Modal -->
<div id="refuseModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('bookings.refuse', $booking) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-ban text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Refuser la réservation
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Vous êtes sur le point de refuser cette réservation.
                                </p>
                                <div class="mt-4">
                                    <label for="refusal_reason" class="block text-sm font-medium text-gray-700">Raison du refus (optionnel)</label>
                                    <textarea name="refusal_reason" id="refusal_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmer le refus
                    </button>
                    <button type="button" onclick="closeRefuseModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Retour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
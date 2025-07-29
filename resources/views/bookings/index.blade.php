@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mes Réservations</h1>
            @if(auth()->user()->role === 'client')
                <a href="{{ route('services.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                    Nouvelle Réservation
                </a>
            @endif
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

        @if($bookings->count() > 0)
            <div class="grid gap-6">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-lg shadow-lg border border-blue-200 p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-2">
                                    <h3 class="text-xl font-semibold text-gray-900">
                                        {{ $booking->service->name }}
                                    </h3>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($booking->status === 'completed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                        @if($booking->status === 'pending') En attente
                                        @elseif($booking->status === 'confirmed') Confirmée
                                        @elseif($booking->status === 'completed') Terminée
                                        @elseif($booking->status === 'cancelled') Annulée
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="text-gray-600 mb-2">
                                    <strong>Numéro de réservation:</strong> {{ $booking->booking_number }}
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                    <div>
                                        <strong>Date et heure:</strong><br>
                                        {{ $booking->start_datetime->format('d/m/Y à H:i') }}
                                        @if($booking->end_datetime)
                                            - {{ $booking->end_datetime->format('H:i') }}
                                        @endif
                                    </div>
                                    
                                    @if(auth()->user()->role === 'client')
                                        <div>
                                            <strong>Prestataire:</strong><br>
                                            {{ $booking->prestataire->user->name }}
                                        </div>
                                    @else
                                        <div>
                                            <strong>Client:</strong><br>
                                            {{ $booking->client->user->name }}
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <strong>Prix:</strong><br>
                                        {{ number_format($booking->total_price, 2) }} €
                                    </div>
                                    
                                    @if($booking->status === 'confirmed' && $booking->confirmed_at)
                                        <div>
                                            <strong>Confirmée le:</strong><br>
                                            {{ $booking->confirmed_at->format('d/m/Y à H:i') }}
                                        </div>
                                    @endif
                                </div>
                                
                                @if($booking->client_notes)
                                    <div class="mt-4 p-3 bg-blue-50 rounded border border-blue-200">
                                        <strong class="text-sm text-blue-700">Notes du client:</strong>
                                        <p class="text-sm text-blue-600 mt-1">{{ $booking->client_notes }}</p>
                                    </div>
                                @endif
                                
                                @if($booking->prestataire_notes)
                                    <div class="mt-4 p-3 bg-blue-50 rounded border border-blue-200">
                                        <strong class="text-sm text-blue-700">Notes du prestataire:</strong>
                                        <p class="text-sm text-blue-600 mt-1">{{ $booking->prestataire_notes }}</p>
                                    </div>
                                @endif
                                
                                @if($booking->status === 'cancelled' && $booking->cancellation_reason)
                                    <div class="mt-4 p-3 bg-red-50 rounded">
                                        <strong class="text-sm text-red-700">Raison de l'annulation:</strong>
                                        <p class="text-sm text-red-600 mt-1">{{ $booking->cancellation_reason }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex flex-col gap-2 ml-4">
                                <a href="{{ route('bookings.show', $booking) }}" 
                                   class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded text-sm transition duration-200 text-center border border-blue-300">
                                    Voir détails
                                </a>
                                
                                @if(auth()->user()->role === 'prestataire')
                                    @if($booking->status === 'pending')
                                        <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition duration-200 w-full">
                                                Confirmer
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'confirmed')
                                        <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition duration-200 w-full">
                                                Marquer terminé
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                
                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                    <button onclick="openCancelModal({{ $booking->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm transition duration-200">
                                        Annuler
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 text-lg mb-4">
                    @if(auth()->user()->role === 'client')
                        Vous n'avez encore aucune réservation.
                    @else
                        Vous n'avez encore reçu aucune réservation.
                    @endif
                </div>
                @if(auth()->user()->role === 'client')
                    <a href="{{ route('services.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200">
                        Découvrir les services
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
</div>

<!-- Modal d'annulation -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6 border border-blue-200 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Annuler la réservation</h3>
            <form id="cancelForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison de l'annulation *
                    </label>
                    <textarea id="cancellation_reason" name="cancellation_reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Veuillez expliquer la raison de l'annulation..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeCancelModal()" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition duration-200">
                        Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCancelModal(bookingId) {
    document.getElementById('cancelForm').action = `/bookings/${bookingId}/cancel`;
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
@endsection
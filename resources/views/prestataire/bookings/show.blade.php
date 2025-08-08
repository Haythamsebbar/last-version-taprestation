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
                <a href="{{ route('prestataire.bookings.index') }}" 
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

                    <!-- Actions selon le statut -->
                    @if($booking->status === 'pending')
                        <div class="mt-6 flex flex-wrap gap-3">
                            <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    <i class="fas fa-check mr-2"></i>Confirmer
                                </button>
                            </form>
                            <form action="{{ route('bookings.refuse', $booking) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    <i class="fas fa-times mr-2"></i>Refuser
                                </button>
                            </form>
                        </div>
                    @elseif($booking->status === 'confirmed')
                        <div class="mt-6 flex flex-wrap gap-3">
                            <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    <i class="fas fa-check-double mr-2"></i>Marquer comme terminé
                                </button>
                            </form>
                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    <i class="fas fa-ban mr-2"></i>Annuler
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Informations du service -->
                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                    <h2 class="text-2xl font-bold text-blue-800 mb-5 border-b-2 border-blue-200 pb-3">Informations du service</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 font-medium">Service:</p>
                            <p class="text-lg font-bold text-blue-900">{{ $booking->service->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-medium">Prix:</p>
                            <p class="text-lg font-bold text-green-600">{{ number_format($booking->service->price, 2) }} €</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-medium">Date de réservation:</p>
                            <p class="text-lg font-bold text-blue-900">{{ $booking->booking_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-medium">Heure:</p>
                            <p class="text-lg font-bold text-blue-900">{{ $booking->booking_time }}</p>
                        </div>
                    </div>
                    @if($booking->service->description)
                        <div class="mt-4">
                            <p class="text-gray-600 font-medium">Description:</p>
                            <p class="text-gray-800">{{ $booking->service->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Informations du client -->
                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                    <h2 class="text-2xl font-bold text-blue-800 mb-5 border-b-2 border-blue-200 pb-3">Informations du client</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 font-medium">Nom:</p>
                            <p class="text-lg font-bold text-blue-900">{{ $booking->client->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-medium">Email:</p>
                            <p class="text-lg font-bold text-blue-900">{{ $booking->client->user->email }}</p>
                        </div>
                        @if($booking->client->user->phone)
                        <div>
                            <p class="text-gray-600 font-medium">Téléphone:</p>
                            <p class="text-lg font-bold text-blue-900">{{ $booking->client->user->phone }}</p>
                        </div>
                        @endif
                        @if($booking->client->address)
                        <div>
                            <p class="text-gray-600 font-medium">Adresse:</p>
                            <p class="text-lg font-bold text-blue-900">{{ $booking->client->address }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($booking->notes)
                <!-- Notes -->
                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                    <h2 class="text-2xl font-bold text-blue-800 mb-5 border-b-2 border-blue-200 pb-3">Notes</h2>
                    <p class="text-gray-800">{{ $booking->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Résumé -->
                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                    <h3 class="text-xl font-bold text-blue-800 mb-4">Résumé</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Numéro:</span>
                            <span class="font-bold">{{ $booking->booking_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date de création:</span>
                            <span class="font-bold">{{ $booking->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Statut:</span>
                            <span class="font-bold
                                @if($booking->status === 'pending') text-yellow-600
                                @elseif($booking->status === 'confirmed') text-green-600
                                @elseif($booking->status === 'completed') text-blue-600
                                @elseif($booking->status === 'cancelled') text-red-600
                                @elseif($booking->status === 'refused') text-red-600
                                @endif">
                                @if($booking->status === 'pending') En attente
                                @elseif($booking->status === 'confirmed') Confirmée
                                @elseif($booking->status === 'completed') Terminée
                                @elseif($booking->status === 'cancelled') Annulée
                                @elseif($booking->status === 'refused') Refusée
                                @endif
                            </span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span class="text-green-600">{{ number_format($booking->service->price, 2) }} €</span>
                        </div>
                    </div>
                </div>

                <!-- Contact rapide -->
                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                    <h3 class="text-xl font-bold text-blue-800 mb-4">Contact rapide</h3>
                    <div class="space-y-3">
                        @if($booking->client->user->phone)
                        <a href="tel:{{ $booking->client->user->phone }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition duration-200">
                            <i class="fas fa-phone text-green-600 mr-3"></i>
                            <span class="text-green-800 font-medium">Appeler</span>
                        </a>
                        @endif
                        <a href="mailto:{{ $booking->client->user->email }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition duration-200">
                            <i class="fas fa-envelope text-blue-600 mr-3"></i>
                            <span class="text-blue-800 font-medium">Envoyer un email</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.status-badge {
    @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium;
}
.status-badge.pending {
    @apply bg-yellow-100 text-yellow-800;
}
.status-badge.confirmed {
    @apply bg-green-100 text-green-800;
}
.status-badge.completed {
    @apply bg-blue-100 text-blue-800;
}
.status-badge.cancelled {
    @apply bg-red-100 text-red-800;
}
.status-badge.refused {
    @apply bg-red-100 text-red-800;
}
</style>
@endsection
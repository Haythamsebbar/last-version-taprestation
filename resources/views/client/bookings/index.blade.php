@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Historique de mes réservations</h1>
                    <p class="text-gray-600 mt-1">Consultez et gérez l'ensemble de vos réservations de services</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('services.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md border border-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Nouvelle réservation
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
        
        <!-- Filtres de réservation -->
        <div class="bg-white rounded-lg shadow-md border border-blue-200 p-4 mb-6">
            <form action="{{ route('client.bookings.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        <option value="refused" {{ request('status') == 'refused' ? 'selected' : '' }}>Refusée</option>
                    </select>
                </div>
                
                <div class="flex-1 min-w-[200px]">
                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                    <select id="date_range" name="date_range" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Toutes les dates</option>
                        <option value="upcoming" {{ request('date_range') == 'upcoming' ? 'selected' : '' }}>À venir</option>
                        <option value="past" {{ request('date_range') == 'past' ? 'selected' : '' }}>Passées</option>
                        <option value="last_month" {{ request('date_range') == 'last_month' ? 'selected' : '' }}>Dernier mois</option>
                        <option value="last_3months" {{ request('date_range') == 'last_3months' ? 'selected' : '' }}>3 derniers mois</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md border border-blue-700">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </div>
                
                @if(request('status') || request('date_range'))
                    <div class="flex items-end">
                        <a href="{{ route('client.bookings.index') }}" class="text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200 border border-gray-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-times mr-2"></i>Réinitialiser
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Bookings List -->
        @if($bookings->count() > 0)
            <div class="space-y-4">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6 hover:shadow-lg hover:border-blue-300 transition-all duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="flex-shrink-0">
                                        @if($booking->prestataire && $booking->prestataire->user && $booking->prestataire->user->profile_photo_path)
                                            <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('storage/' . $booking->prestataire->user->profile_photo_path) }}" alt="{{ $booking->prestataire->user->name }}">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                                {{ $booking->prestataire && $booking->prestataire->user ? strtoupper(substr($booking->prestataire->user->name, 0, 1)) : 'P' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $booking->service ? $booking->service->name : 'Service supprimé' }}</h3>
                                        <p class="text-gray-600">avec {{ $booking->prestataire && $booking->prestataire->user ? $booking->prestataire->user->name : 'Prestataire supprimé' }}</p>
                                        <span class="text-xs text-gray-500">Réservation #{{ $booking->id }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Date et heure</p>
                                        <p class="font-medium text-gray-900">
                                            {{ $booking->start_datetime->format('d/m/Y à H:i') }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Durée</p>
                                        <p class="font-medium text-gray-900">
                                            {{ $booking->start_datetime->diffInMinutes($booking->end_datetime) }} minutes
                                        </p>
                                    </div>
                                    <div>
                                        <!-- Prix supprimé pour des raisons de confidentialité -->
                                <p class="text-sm text-gray-500">Statut</p>
                                <p class="font-medium text-gray-900">{{ ucfirst($booking->status) }}</p>
                                    </div>
                                </div>

                                @if($booking->client_notes)
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-500">Notes</p>
                                        <p class="text-gray-700">{{ $booking->client_notes }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col items-end space-y-3">
                                <!-- Status Badge -->
                                <span class="px-3 py-1 rounded-full text-sm font-medium flex items-center justify-center shadow-sm border
                                    @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 border-yellow-200
                                    @elseif($booking->status === 'confirmed') bg-green-100 text-green-800 border-green-200
                                    @elseif($booking->status === 'completed') bg-blue-100 text-blue-800 border-blue-200
                                    @elseif($booking->status === 'cancelled') bg-gray-100 text-gray-800 border-gray-200
                                    @elseif($booking->status === 'refused') bg-red-100 text-red-800 border-red-200
                                    @else bg-gray-100 text-gray-800 border-gray-200
                                    @endif">
                                    @if($booking->status === 'pending')
                                        <i class="fas fa-clock mr-1"></i> En attente de confirmation
                                    @elseif($booking->status === 'confirmed')
                                        <i class="fas fa-check-circle mr-1"></i> Confirmée par le prestataire
                                    @elseif($booking->status === 'completed')
                                        <i class="fas fa-check-double mr-1"></i> Terminée
                                    @elseif($booking->status === 'cancelled')
                                        <i class="fas fa-times-circle mr-1"></i> Annulée
                                    @elseif($booking->status === 'refused')
                                        <i class="fas fa-times-circle mr-1"></i> Refusée par le prestataire
                                    @else
                                        {{ ucfirst($booking->status) }}
                                    @endif
                                </span>

                                <!-- Actions -->
                                <div class="flex flex-col space-y-2">
                                    <a href="{{ route('bookings.show', $booking) }}" class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-md text-sm font-medium flex items-center justify-center border border-blue-200 shadow-sm hover:shadow transition-all duration-200">
                                        <i class="fas fa-eye mr-1.5"></i> Voir détails
                                    </a>
                                    
                                    @if($booking->status === 'pending')
                                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="cancellation_reason" value="Annulée par le client">
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-md text-sm font-medium flex items-center justify-center w-full border border-red-200 shadow-sm hover:shadow transition-all duration-200">
                                                <i class="fas fa-times mr-1.5"></i> Annuler
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'confirmed')
                                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="cancellation_reason" value="Annulée par le client">
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-md text-sm font-medium flex items-center justify-center w-full border border-red-200 shadow-sm hover:shadow transition-all duration-200">
                                                <i class="fas fa-times mr-1.5"></i> Annuler
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'completed')
                                        <a href="#" class="bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-1.5 rounded-md text-sm font-medium flex items-center justify-center border border-purple-200 shadow-sm hover:shadow transition-all duration-200">
                                            <i class="fas fa-star mr-1.5"></i> Évaluer
                                        </a>
                                        <a href="#" class="bg-gray-50 hover:bg-gray-100 text-gray-700 px-3 py-1.5 rounded-md text-sm font-medium flex items-center justify-center border border-gray-200 shadow-sm hover:shadow transition-all duration-200">
                                            <i class="fas fa-redo mr-1.5"></i> Réserver à nouveau
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-md border border-blue-200 p-12 text-center bg-gradient-to-br from-white to-blue-50">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-calendar-times text-6xl text-blue-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune réservation</h3>
                    <p class="text-gray-600 mb-6">Vous n'avez pas encore de réservations. Explorez nos services pour commencer.</p>
                    <a href="{{ route('services.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md border border-blue-700 inline-flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Découvrir les services
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
</div>
@endsection
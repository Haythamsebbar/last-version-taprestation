@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestion des Activités</h1>
                    <p class="text-gray-600 mt-1">Consultez et gérez vos réservations, locations d'équipements et ventes urgentes</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('prestataire.services.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-cog mr-2"></i>
                        Gérer mes services
                    </a>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="mt-4">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('prestataire.bookings.index') }}" class="{{ !request('type') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Toutes les activités
                        </a>
                        <a href="{{ route('prestataire.bookings.index', ['type' => 'bookings']) }}" class="{{ request('type') === 'bookings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Réservations ({{ $bookings->count() }})
                        </a>
                        <a href="{{ route('prestataire.bookings.index', ['type' => 'equipment']) }}" class="{{ request('type') === 'equipment' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            <i class="fas fa-tools mr-2"></i>
                            Équipements ({{ $equipmentRentals->count() }})
                        </a>
                        <a href="{{ route('prestataire.bookings.index', ['type' => 'urgent_sales']) }}" class="{{ request('type') === 'urgent_sales' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            <i class="fas fa-fire mr-2"></i>
                            Ventes urgentes ({{ $urgentSales->count() }})
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="mt-4">
                <form action="{{ route('prestataire.bookings.index') }}" method="GET" class="flex flex-col md:flex-row md:items-end space-y-3 md:space-y-0 md:space-x-4">
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    
                    <div class="flex-1">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">Tous les statuts</option>
                            @if(request('type') === 'equipment')
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                                <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>En préparation</option>
                                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Livrée</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Retournée</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminée</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            @elseif(request('type') === 'urgent_sales')
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Vendue</option>
                                <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Retirée</option>
                                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Bloquée</option>
                            @else
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmées</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminées</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulées</option>
                                <option value="refused" {{ request('status') === 'refused' ? 'selected' : '' }}>Refusées</option>
                            @endif
                        </select>
                    </div>
                    
                    <div class="flex-1">
                        <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                        <select id="date_range" name="date_range" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">Toutes les dates</option>
                            <option value="upcoming" {{ request('date_range') === 'upcoming' ? 'selected' : '' }}>À venir</option>
                            <option value="past" {{ request('date_range') === 'past' ? 'selected' : '' }}>Passées</option>
                            <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="this_week" {{ request('date_range') === 'this_week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>Ce mois-ci</option>
                        </select>
                    </div>
                    
                    @if(!request('type') || request('type') === 'bookings')
                    <div class="flex-1">
                        <label for="service" class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                        <select id="service" name="service_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">Tous les services</option>
                            @foreach(auth()->user()->prestataire->services as $service)
                                <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-filter mr-1"></i> Filtrer
                        </button>
                        <a href="{{ route('prestataire.bookings.index', ['type' => request('type')]) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                            <i class="fas fa-redo mr-1"></i> Réinitialiser
                        </a>
                    </div>
                </form>
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

        <!-- Activities List -->
        @php
            $hasItems = false;
            if (!request('type')) {
                $hasItems = $bookings->count() > 0 || $equipmentRentals->count() > 0 || (isset($equipmentRentalRequests) && $equipmentRentalRequests->count() > 0) || $urgentSales->count() > 0;
            } elseif (request('type') === 'bookings') {
                $hasItems = $bookings->count() > 0;
            } elseif (request('type') === 'equipment') {
                $hasItems = $equipmentRentals->count() > 0 || (isset($equipmentRentalRequests) && $equipmentRentalRequests->count() > 0);
            } elseif (request('type') === 'urgent_sales') {
                $hasItems = $urgentSales->count() > 0;
            }
        @endphp
        
        @if($hasItems)
            <div class="space-y-4">
                {{-- Service Bookings --}}
                @if(!request('type') || request('type') === 'bookings')
                    @foreach($bookings as $booking)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-calendar-alt text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">RÉSERVATION</span>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $booking->service->name }}</h3>
                                            <p class="text-gray-600">Client: {{ $booking->client->user->name }}</p>
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
                                            <p class="text-sm text-gray-500">Statut</p>
                                            <p class="font-medium text-gray-900">{{ ucfirst($booking->status) }}</p>
                                        </div>
                                    </div>

                                    @if($booking->client_notes)
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-500">Notes du client</p>
                                            <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $booking->client_notes }}</p>
                                        </div>
                                    @endif

                                    @if($booking->cancellation_reason)
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-500">Raison d'annulation</p>
                                            <p class="text-red-700 bg-red-50 p-3 rounded-lg">{{ $booking->cancellation_reason }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end space-y-3">
                                    <!-- Status Badge -->
                                    @php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-clock', 'text' => 'En attente'],
                                            'confirmed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle', 'text' => 'Confirmée'],
                                            'completed' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-check-double', 'text' => 'Terminée'],
                                            'cancelled' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle', 'text' => 'Annulée'],
                                            'refused' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-ban', 'text' => 'Refusée'],
                                        ];
                                        $currentStatus = $statusConfig[$booking->status] ?? null;
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-sm font-medium flex items-center justify-center {{ $currentStatus['class'] ?? 'bg-gray-100 text-gray-800' }}">
                                        @if($currentStatus)
                                            <i class="fas {{ $currentStatus['icon'] }} mr-1"></i> {{ $currentStatus['text'] }}
                                        @else
                                            {{ ucfirst($booking->status) }}
                                        @endif
                                    </span>

                                    <!-- Actions -->
                                    <div class="flex flex-col space-y-2">
                                        <a href="{{ route('bookings.show', $booking) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm font-medium flex items-center justify-center">
                                            <i class="fas fa-eye mr-1"></i> Voir détails
                                        </a>
                                        
                                        @if($booking->status === 'pending')
                                            <form action="#" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded text-sm font-medium flex items-center justify-center w-full">
                                                    <i class="fas fa-check mr-1"></i> Confirmer
                                                </button>
                                            </form>
                                            <form action="{{ route('bookings.refuse', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir refuser cette réservation ?')">
                                                @csrf
                                                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded text-sm font-medium flex items-center justify-center w-full">
                                                    <i class="fas fa-times mr-1"></i> Refuser
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($booking->status === 'confirmed')
                                            <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm font-medium flex items-center justify-center w-full">
                                                    <i class="fas fa-check-double mr-1"></i> Marquer terminé
                                                </button>
                                            </form>
                                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="cancellation_reason" value="Annulée par le prestataire">
                                                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded text-sm font-medium flex items-center justify-center w-full">
                                                    <i class="fas fa-times mr-1"></i> Annuler
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($booking->status === 'completed')
                                            <a href="#" class="bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded text-sm font-medium flex items-center justify-center">
                                                <i class="fas fa-file-alt mr-1"></i> Voir facture
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                
                {{-- Equipment Rentals --}}
                @if(!request('type') || request('type') === 'equipment')
                    @foreach($equipmentRentals as $rental)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-tools text-orange-600"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded">ÉQUIPEMENT</span>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $rental->equipment->name }}</h3>
                                            <p class="text-gray-600">Client: {{ $rental->client->user->name }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Période de location</p>
                                            <p class="font-medium text-gray-900">
                                                Du {{ $rental->start_date->format('d/m/Y') }} au {{ $rental->end_date->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Prix total</p>
                                            <p class="font-medium text-gray-900">
                                                {{ number_format($rental->total_price, 2) }}€
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Statut</p>
                                            <p class="font-medium text-gray-900">{{ ucfirst($rental->status) }}</p>
                                        </div>
                                    </div>

                                    @if($rental->notes)
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-500">Notes</p>
                                            <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $rental->notes }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end space-y-3">
                                    @php
                                        $rentalStatusConfig = [
                                            'confirmed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle', 'text' => 'Confirmée'],
                                            'preparing' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-cog', 'text' => 'En préparation'],
                                            'delivered' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-truck', 'text' => 'Livrée'],
                                            'active' => ['class' => 'bg-purple-100 text-purple-800', 'icon' => 'fa-play', 'text' => 'Active'],
                                            'returned' => ['class' => 'bg-indigo-100 text-indigo-800', 'icon' => 'fa-undo', 'text' => 'Retournée'],
                                            'completed' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-check-double', 'text' => 'Terminée'],
                                            'cancelled' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle', 'text' => 'Annulée'],
                                        ];
                                        $currentRentalStatus = $rentalStatusConfig[$rental->status] ?? null;
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-sm font-medium flex items-center justify-center {{ $currentRentalStatus['class'] ?? 'bg-gray-100 text-gray-800' }}">
                                        @if($currentRentalStatus)
                                            <i class="fas {{ $currentRentalStatus['icon'] }} mr-1"></i> {{ $currentRentalStatus['text'] }}
                                        @else
                                            {{ ucfirst($rental->status) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Equipment Rental Requests --}}
                    @foreach($equipmentRentalRequests as $request)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-clock text-yellow-600"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">DEMANDE DE LOCATION</span>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $request->equipment->name }}</h3>
                                            <p class="text-gray-600">Client: {{ $request->client->user->name }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Période demandée</p>
                                            <p class="font-medium text-gray-900">
                                                Du {{ $request->start_date->format('d/m/Y') }} au {{ $request->end_date->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Prix proposé</p>
                                            <p class="font-medium text-gray-900">
                                                {{ number_format($request->total_amount, 2) }}€
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Statut</p>
                                            <p class="font-medium text-gray-900">{{ ucfirst($request->status) }}</p>
                                        </div>
                                    </div>

                                    @if($request->client_message)
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-500">Message du client</p>
                                            <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $request->client_message }}</p>
                                        </div>
                                    @endif

                                    @if($request->rejection_reason)
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-500">Raison du refus</p>
                                            <p class="text-red-700 bg-red-50 p-3 rounded-lg">{{ $request->rejection_reason }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end space-y-3">
                                    @php
                                        $requestStatusConfig = [
                                            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-clock', 'text' => 'En attente'],
                                            'accepted' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle', 'text' => 'Acceptée'],
                                            'rejected' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle', 'text' => 'Refusée'],
                                            'cancelled' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-ban', 'text' => 'Annulée'],
                                            'expired' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-clock', 'text' => 'Expirée'],
                                        ];
                                        $currentRequestStatus = $requestStatusConfig[$request->status] ?? null;
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-sm font-medium flex items-center justify-center {{ $currentRequestStatus['class'] ?? 'bg-gray-100 text-gray-800' }}">
                                        @if($currentRequestStatus)
                                            <i class="fas {{ $currentRequestStatus['icon'] }} mr-1"></i> {{ $currentRequestStatus['text'] }}
                                        @else
                                            {{ ucfirst($request->status) }}
                                        @endif
                                    </span>

                                    <!-- Actions -->
                                    <div class="flex flex-col space-y-2">
                                        @if($request->status === 'pending')
                                            <form action="{{ route('prestataire.equipment-rental-requests.accept', $request) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded text-sm font-medium flex items-center justify-center w-full">
                                                    <i class="fas fa-check mr-1"></i> Accepter
                                                </button>
                                            </form>
                                            <form action="{{ route('prestataire.equipment-rental-requests.reject', $request) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir refuser cette demande ?')">
                                                @csrf
                                                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded text-sm font-medium flex items-center justify-center w-full">
                                                    <i class="fas fa-times mr-1"></i> Refuser
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('prestataire.equipment-rental-requests.show', $request) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm font-medium flex items-center justify-center">
                                            <i class="fas fa-eye mr-1"></i> Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                
                {{-- Urgent Sales --}}
                @if(!request('type') || request('type') === 'urgent_sales')
                    @foreach($urgentSales as $sale)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-fire text-red-600"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">VENTE URGENTE</span>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $sale->title }}</h3>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Prix</p>
                                            <p class="font-medium text-gray-900">
                                                {{ number_format($sale->price, 2) }}€
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">État</p>
                                            <p class="font-medium text-gray-900">
                                                {{ ucfirst($sale->condition) }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Statut</p>
                                            <p class="font-medium text-gray-900">{{ ucfirst($sale->status) }}</p>
                                        </div>
                                    </div>

                                    @if($sale->description)
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-500">Description</p>
                                            <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">{{ Str::limit($sale->description, 200) }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end space-y-3">
                                    @php
                                        $saleStatusConfig = [
                                            'active' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle', 'text' => 'Active'],
                                            'sold' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-handshake', 'text' => 'Vendue'],
                                            'withdrawn' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-eye-slash', 'text' => 'Retirée'],
                                            'blocked' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-ban', 'text' => 'Bloquée'],
                                        ];
                                        $currentSaleStatus = $saleStatusConfig[$sale->status] ?? null;
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-sm font-medium flex items-center justify-center {{ $currentSaleStatus['class'] ?? 'bg-gray-100 text-gray-800' }}">
                                        @if($currentSaleStatus)
                                            <i class="fas {{ $currentSaleStatus['icon'] }} mr-1"></i> {{ $currentSaleStatus['text'] }}
                                        @else
                                            {{ ucfirst($sale->status) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            
        
            
        @endif
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Mes Demandes')

@section('content')
<div class="bg-blue-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- En-tête -->
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-extrabold text-blue-900 mb-2">Mes Demandes</h1>
                <p class="text-lg text-blue-700">Gérez toutes vos demandes de services, équipements et ventes urgentes</p>
            </div>

            <!-- Messages de session -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6 shadow-md">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 shadow-md">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filtres (optionnel) -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                    <h3 class="text-lg font-bold text-blue-800 mb-4">Filtrer par type</h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('prestataire.bookings.index') }}" 
                           class="px-6 py-3 rounded-lg font-semibold {{ !request('type') ? 'bg-blue-600 text-white shadow-lg' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200' }} transition duration-200">
                            Toutes
                        </a>
                        <a href="{{ route('prestataire.bookings.index', ['type' => 'service']) }}" 
                           class="px-6 py-3 rounded-lg font-semibold {{ request('type') === 'service' ? 'bg-blue-600 text-white shadow-lg' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200' }} transition duration-200">
                            Services
                        </a>
                        <a href="{{ route('prestataire.bookings.index', ['type' => 'equipment']) }}" 
                           class="px-6 py-3 rounded-lg font-semibold {{ request('type') === 'equipment' ? 'bg-blue-600 text-white shadow-lg' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200' }} transition duration-200">
                            Équipements
                        </a>
                        <a href="{{ route('prestataire.bookings.index', ['type' => 'urgent_sale']) }}" 
                           class="px-6 py-3 rounded-lg font-semibold {{ request('type') === 'urgent_sale' ? 'bg-blue-600 text-white shadow-lg' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200' }} transition duration-200">
                            Ventes Urgentes
                        </a>
                    </div>
                </div>
            </div>

    @php
        // Vérifier s'il y a des données à afficher
        $hasItems = ($showServices && isset($serviceBookings) && $serviceBookings->count() > 0) ||
                   ($showEquipments && isset($equipmentRentalRequests) && $equipmentRentalRequests->count() > 0) ||
                   ($showUrgentSales && isset($urgentSales) && $urgentSales->count() > 0);
    @endphp

            @if($hasItems)
                <!-- Section Services -->
                @if($showServices && isset($serviceBookings) && $serviceBookings->count() > 0)
                    <div class="mb-8">
                        <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                            <div class="flex items-center mb-6 border-b-2 border-blue-200 pb-4">
                                <div class="w-4 h-4 bg-blue-600 rounded-full mr-3"></div>
                                <h2 class="text-2xl font-bold text-blue-800">Services</h2>
                                <span class="ml-3 bg-blue-100 text-blue-800 text-sm font-bold px-3 py-1 rounded-full">{{ $serviceBookings->count() }}</span>
                            </div>
                            
                            <div class="space-y-4">
                                @foreach($serviceBookings as $booking)
                                    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg shadow-sm p-6 hover:shadow-lg transition-all duration-200">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-3">
                                                    <h3 class="text-lg font-bold text-blue-900 mr-3">{{ $booking->service->title ?? 'Service' }}</h3>
                                                    <span class="px-3 py-1 text-xs font-bold rounded-full
                                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($booking->status === 'accepted') bg-green-100 text-green-800
                                                        @elseif($booking->status === 'rejected') bg-red-100 text-red-800
                                                        @endif">
                                                        @if($booking->status === 'pending') En attente
                                                        @elseif($booking->status === 'accepted') Acceptée
                                                        @elseif($booking->status === 'rejected') Refusée
                                                        @endif
                                                    </span>
                                                </div>
                                                
                                                <div class="space-y-2">
                                                    <p class="text-blue-800 font-medium"><strong>Client:</strong> {{ $booking->client->user->name ?? 'N/A' }}</p>
                                                    <p class="text-blue-700"><strong>Type:</strong> Service</p>
                                                    @if($booking->description)
                                                        <p class="text-blue-700"><strong>Description:</strong> {{ Str::limit($booking->description, 100) }}</p>
                                                    @endif
                                                    <p class="text-sm text-blue-600">{{ $booking->created_at->format('d/m/Y à H:i') }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-col space-y-2 ml-4">
                                                <a href="{{ route('prestataire.bookings.show', $booking->id) }}" 
                                                   class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition duration-200 text-center shadow-md hover:shadow-lg">
                                                    Voir détails
                                                </a>
                                                
                                                @if($booking->status === 'pending')
                                                    <form action="{{ route('prestataire.bookings.accept', $booking->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition duration-200 shadow-md hover:shadow-lg">
                                                            Accepter
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('prestataire.bookings.reject', $booking->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700 transition duration-200 shadow-md hover:shadow-lg">
                                                            Refuser
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Section Équipements -->
                @if($showEquipments && isset($equipmentRentalRequests) && $equipmentRentalRequests->count() > 0)
                    <div class="mb-8">
                        <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                            <div class="flex items-center mb-6 border-b-2 border-blue-200 pb-4">
                                <div class="w-4 h-4 bg-blue-600 rounded-full mr-3"></div>
                                <h2 class="text-2xl font-bold text-blue-800">Équipements à louer</h2>
                                <span class="ml-3 bg-blue-100 text-blue-800 text-sm font-bold px-3 py-1 rounded-full">{{ $equipmentRentalRequests->count() }}</span>
                            </div>
                            
                            <div class="space-y-4">
                                @foreach($equipmentRentalRequests as $request)
                                    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg shadow-sm p-6 hover:shadow-lg transition-all duration-200">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-3">
                                                    <h3 class="text-lg font-bold text-blue-900 mr-3">{{ $request->equipment->name ?? 'Équipement' }}</h3>
                                                    <span class="px-3 py-1 text-xs font-bold rounded-full
                                                        @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($request->status === 'accepted') bg-green-100 text-green-800
                                                        @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                                        @endif">
                                                        @if($request->status === 'pending') En attente
                                                        @elseif($request->status === 'accepted') Acceptée
                                                        @elseif($request->status === 'rejected') Refusée
                                                        @endif
                                                    </span>
                                                </div>
                                                
                                                <div class="space-y-2">
                                                    <p class="text-blue-800 font-medium"><strong>Client:</strong> {{ $request->client->user->name ?? 'N/A' }}</p>
                                                    <p class="text-blue-700"><strong>Type:</strong> Demande de location d'équipement</p>
                                                    @if($request->equipment->description)
                                                        <p class="text-blue-700"><strong>Description:</strong> {{ Str::limit($request->equipment->description, 100) }}</p>
                                                    @endif
                                                    <p class="text-blue-700"><strong>Date de début:</strong> {{ $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y H:i') : 'Non spécifiée' }}</p>
                                                    <p class="text-blue-700"><strong>Date de fin:</strong> {{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d/m/Y H:i') : 'Non spécifiée' }}</p>
                                                    <p class="text-sm text-blue-600">{{ $request->created_at->format('d/m/Y à H:i') }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-col space-y-2 ml-4">
                                                <a href="{{ route('prestataire.equipment-rental-requests.show', $request->id) }}" 
                                                   class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition duration-200 text-center shadow-md hover:shadow-lg">
                                                    Voir détails
                                                </a>
                                                
                                                @if($request->status === 'pending')
                                                    <form action="{{ route('prestataire.equipment-rental-requests.accept', $request->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition duration-200 shadow-md hover:shadow-lg">
                                                            Accepter
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('prestataire.equipment-rental-requests.reject', $request->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700 transition duration-200 shadow-md hover:shadow-lg">
                                                            Refuser
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Section Ventes Urgentes -->
                @if($showUrgentSales && isset($urgentSales) && $urgentSales->count() > 0)
                    <div class="mb-8">
                        <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6">
                            <div class="flex items-center mb-6 border-b-2 border-blue-200 pb-4">
                                <div class="w-4 h-4 bg-blue-600 rounded-full mr-3"></div>
                                <h2 class="text-2xl font-bold text-blue-800">Ventes urgentes</h2>
                                <span class="ml-3 bg-blue-100 text-blue-800 text-sm font-bold px-3 py-1 rounded-full">{{ $urgentSales->count() }}</span>
                            </div>
                            
                            <div class="space-y-4">
                                @foreach($urgentSales as $sale)
                                    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg shadow-sm p-6 hover:shadow-lg transition-all duration-200">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-3">
                                                    <h3 class="text-lg font-bold text-blue-900 mr-3">{{ $sale->title ?? 'Vente urgente' }}</h3>
                                                    <span class="px-3 py-1 text-xs font-bold rounded-full
                                                        @if($sale->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($sale->status === 'accepted') bg-green-100 text-green-800
                                                        @elseif($sale->status === 'rejected') bg-red-100 text-red-800
                                                        @endif">
                                                        @if($sale->status === 'pending') En attente
                                                        @elseif($sale->status === 'accepted') Acceptée
                                                        @elseif($sale->status === 'rejected') Refusée
                                                        @endif
                                                    </span>
                                                </div>
                                                
                                                <div class="space-y-2">
                                                    <p class="text-blue-800 font-medium"><strong>Client:</strong> {{ $sale->client->name ?? 'N/A' }}</p>
                                                    <p class="text-blue-700"><strong>Type:</strong> Vente</p>
                                                    @if($sale->description)
                                                        <p class="text-blue-700"><strong>Description:</strong> {{ Str::limit($sale->description, 100) }}</p>
                                                    @endif
                                                    <p class="text-sm text-blue-600">{{ $sale->created_at->format('d/m/Y à H:i') }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-col space-y-2 ml-4">
                                                <a href="{{ route('prestataire.urgent-sales.show', $sale->id) }}" 
                                                   class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition duration-200 text-center shadow-md hover:shadow-lg">
                                                    Voir détails
                                                </a>
                                                
                                                @if($sale->status === 'pending')
                                                    <form action="{{ route('prestataire.urgent-sales.accept', $sale->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition duration-200 shadow-md hover:shadow-lg">
                                                            Accepter
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('prestataire.urgent-sales.reject', $sale->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700 transition duration-200 shadow-md hover:shadow-lg">
                                                            Refuser
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @else
        <!-- État vide -->
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune demande</h3>
            <p class="text-gray-500">Vous n'avez aucune demande pour le moment.</p>
        </div>
    @endif
</div>

<script>
// Script pour les notifications en temps réel (à implémenter plus tard)
document.addEventListener('DOMContentLoaded', function() {
    // Ici on pourra ajouter la logique pour les notifications en temps réel
    // et la mise à jour automatique des statuts
});
</script>
@endsection
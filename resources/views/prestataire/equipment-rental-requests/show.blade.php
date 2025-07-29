@extends('layouts.app')

@section('title', 'Demande de location #' . $request->id)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('prestataire.equipment-rental-requests.index') }}" 
                       class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Demande de location #{{ $request->id }}</h1>
                        <p class="text-gray-600">{{ $request->equipment->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($request->status === 'accepted') bg-green-100 text-green-800
                        @elseif($request->status === 'rejected') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        @if($request->status === 'pending') En attente
                        @elseif($request->status === 'accepted') Acceptée
                        @elseif($request->status === 'rejected') Refusée
                        @else {{ ucfirst($request->status) }} @endif
                    </span>
                    
                    @if($request->status === 'pending')
                    <div class="flex space-x-2">
                        <form method="POST" action="{{ route('prestataire.equipment-rental-requests.accept', $request) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200"
                                    onclick="return confirm('Accepter cette demande de location ?')">
                                ✅ Accepter
                            </button>
                        </form>
                        
                        <button type="button" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200"
                                x-data
                                @click="$dispatch('open-modal', 'reject-request')">
                            ❌ Refuser
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations du client -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">👤 Informations du client</h2>
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-lg">
                                {{ substr($request->client->first_name, 0, 1) }}{{ substr($request->client->last_name, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ $request->client->first_name }} {{ $request->client->last_name }}</h3>
                            <div class="mt-2 space-y-1">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Email:</span> {{ $request->client->email }}
                                </p>
                                @if($request->client->phone)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Téléphone:</span> {{ $request->client->phone }}
                                </p>
                                @endif
                                @if($request->client->address)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Adresse:</span> {{ $request->client->address }}
                                </p>
                                @endif
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Membre depuis:</span> {{ $request->client->created_at->format('F Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Détails de l'équipement -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">🔧 Équipement demandé</h2>
                    <div class="flex items-start space-x-4">
                        @if($request->equipment->main_photo)
                        <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="{{ Storage::url($request->equipment->main_photo) }}" 
                                 alt="{{ $request->equipment->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ $request->equipment->name }}</h3>
                            <p class="text-gray-600 mb-2">{{ $request->equipment->brand }} {{ $request->equipment->model }}</p>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">État:</span>
                                    <span class="text-gray-600">{{ $request->equipment->formatted_condition }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Prix/jour:</span>
                                    <span class="text-gray-600">{{ number_format($request->equipment->daily_rate, 2) }}€</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('prestataire.equipment.show', $request->equipment) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Voir la fiche complète →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Détails de la demande -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Détails de la demande</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Période de location</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Début:</span>
                                    <span class="font-medium">{{ $request->start_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Fin:</span>
                                    <span class="font-medium">{{ $request->end_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Durée:</span>
                                    <span class="font-medium">{{ $request->start_date->diffInDays($request->end_date) + 1 }} jour(s)</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Options</h3>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    @if($request->delivery_required)
                                        <span class="text-green-600">✅</span>
                                        <span class="text-sm">Livraison demandée</span>
                                    @else
                                        <span class="text-gray-400">❌</span>
                                        <span class="text-sm text-gray-600">Récupération sur place</span>
                                    @endif
                                </div>
                                
                                @if($request->delivery_required && $request->delivery_address)
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Adresse de livraison:</span><br>
                                    {{ $request->delivery_address }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($request->message)
                    <div class="mt-6">
                        <h3 class="font-medium text-gray-900 mb-2">Message du client</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700">{{ $request->message }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Historique -->
                @if($request->status !== 'pending')
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Historique</h2>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Demande créée</p>
                                <p class="text-xs text-gray-500">{{ $request->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($request->status === 'accepted')
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Demande acceptée</p>
                                <p class="text-xs text-gray-500">{{ $request->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @elseif($request->status === 'rejected')
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Demande refusée</p>
                                <p class="text-xs text-gray-500">{{ $request->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Récapitulatif financier -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">💰 Récapitulatif</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Location ({{ $request->start_date->diffInDays($request->end_date) + 1 }} jours):</span>
                            <span class="font-medium">{{ number_format($request->rental_amount, 2) }}€</span>
                        </div>
                        
                        @if($request->delivery_required && $request->delivery_cost > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Livraison:</span>
                            <span class="font-medium">{{ number_format($request->delivery_cost, 2) }}€</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <span class="font-medium text-gray-900">Total:</span>
                            <span class="font-bold text-lg text-blue-600">{{ number_format($request->total_amount, 2) }}€</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Caution:</span>
                            <span class="font-medium text-gray-900">{{ number_format($request->deposit_amount, 2) }}€</span>
                        </div>
                    </div>
                </div>
                
                <!-- Informations complémentaires -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">ℹInformations</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Demande créée:</span><br>
                            <span class="text-gray-600">{{ $request->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        
                        @if($request->status !== 'pending')
                        <div>
                            <span class="font-medium text-gray-700">Dernière mise à jour:</span><br>
                            <span class="text-gray-600">{{ $request->updated_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @endif
                        
                        <div>
                            <span class="font-medium text-gray-700">Référence:</span><br>
                            <span class="text-gray-600 font-mono">#{{ $request->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="reject-request" :show="$errors->rejectRequest->isNotEmpty()" focusable>
        <form method="post" action="{{ route('prestataire.equipment-rental-requests.reject', $request) }}" class="p-6">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Êtes-vous sûr de vouloir refuser cette demande ?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Veuillez fournir la raison du refus. Cette information sera communiquée au client.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="rejection_reason" value="{{ __('Raison du refus') }}" class="sr-only" />

                <x-textarea-input
                    id="rejection_reason"
                    name="rejection_reason"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Raison du refus') }}"
                    required
                ></x-textarea-input>

                <x-input-error :messages="$errors->rejectRequest->get('rejection_reason')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Annuler') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Refuser la demande') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
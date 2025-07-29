@extends('layouts.app')

@section('content')
<div class="py-10">
    <header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">Détails de la Mission: {{ $mission->title }}</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Publiée le {{ $mission->created_at->format('d/m/Y') }} · 
                        Délai: {{ $mission->deadline }}
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('prestataire.missions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Retour aux missions
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Statut de la demande -->
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($mission->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                        @if($mission->status == 'in_progress') bg-blue-100 text-blue-800 @endif
                        @if($mission->status == 'completed') bg-green-100 text-green-800 @endif
                        @if($mission->status == 'cancelled') bg-red-100 text-red-800 @endif
                    ">
                        @if($mission->status == 'pending') En attente @endif
                        @if($mission->status == 'in_progress') En cours @endif
                        @if($mission->status == 'completed') Terminée @endif
                        @if($mission->status == 'cancelled') Annulée @endif
                    </span>
                </div>

                <!-- Détails de la mission -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Détails de la mission</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $mission->category_name }}</dd>
                            </div>
                            @if($mission->subcategory_name)
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Sous-catégorie</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $mission->subcategory_name }}</dd>
                            </div>
                            @endif
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Délai</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $mission->deadline }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $mission->description }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Informations sur le client -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Informations sur le client</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Nom</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $mission->client && $mission->client->user ? $mission->client->user->name : 'Client non disponible' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $mission->client && $mission->client->user ? $mission->client->user->email : 'Email non disponible' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Votre Offre -->
                @if($offer)
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Votre Offre</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Montant</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $offer->amount }} €</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($offer->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                        @if($offer->status == 'accepted') bg-green-100 text-green-800 @endif
                                        @if($offer->status == 'rejected') bg-red-100 text-red-800 @endif
                                    ">
                                        @if($offer->status == 'pending') En attente @endif
                                        @if($offer->status == 'accepted') Acceptée @endif
                                        @if($offer->status == 'rejected') Refusée @endif
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Message</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $offer->message }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
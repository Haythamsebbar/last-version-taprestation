@extends('layouts.app')

@section('title', 'Mes offres reçues')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Mes offres reçues</h1>
                    <p class="mt-2 text-gray-600">Gérez toutes les offres reçues pour vos demandes de service</p>
                </div>
                <a href="{{ route('client.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour au tableau de bord
                </a>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingOffers->count() }}</p>
                        <p class="text-sm text-gray-600">En attente</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $acceptedOffers->count() }}</p>
                        <p class="text-sm text-gray-600">Acceptées</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $rejectedOffers->count() }}</p>
                        <p class="text-sm text-gray-600">Refusées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offres en attente -->
        @if($pendingOffers->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Offres en attente de réponse
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ $pendingOffers->count() }}
                    </span>
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($pendingOffers as $offer)
                        @if($offer->prestataire)
                        <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200 rounded-lg p-6 hover:shadow-md transition-all duration-300">
                            <!-- En-tête de l'offre -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                                        @if($offer->prestataire->user->avatar)
                                            <img src="{{ asset('storage/' . $offer->prestataire->user->avatar) }}" alt="{{ $offer->prestataire->user->name }}" class="w-12 h-12 rounded-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $offer->prestataire->user->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $offer->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    Nouveau
                                </span>
                            </div>
                            
                            <!-- Détails de l'offre -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm text-gray-600">Pour:</span>
                                    <a href="{{ route('client.requests.show', $offer->clientRequest->id) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                        {{ Str::limit($offer->clientRequest->title, 40) }}
                                    </a>
                                </div>
                                <div class="flex items-center justify-between mb-3">
                                    <!-- Prix supprimé pour des raisons de confidentialité -->
                                </div>
                                @if($offer->message)
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-700 bg-white p-3 rounded border-l-4 border-orange-400">
                                            "{{ $offer->message }}"
                                        </p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex space-x-3">
                                <form action="{{ route('client.offers.accept', $offer->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Êtes-vous sûr de vouloir accepter cette offre ? Cela désactivera automatiquement les autres offres pour cette demande.')">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Accepter
                                    </button>
                                </form>
                                <form action="{{ route('client.offers.reject', $offer->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class_="w-full bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors duration-200 flex items-center justify-center" onclick="return confirm('Êtes-vous sûr de vouloir refuser cette offre ?')">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Refuser
                                    </button>
                                </form>
                                <a href="{{ route('client.requests.show', $offer->clientRequest->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Historique des offres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Historique des offres
                </h2>
            </div>
            <div class="p-6">
                @if($offers->count() > 0)
                    <div class="space-y-4">
                        @foreach($offers as $offer)
                            @if($offer->prestataire)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            @if($offer->prestataire->user->avatar)
                                                <img src="{{ asset('storage/' . $offer->prestataire->user->avatar) }}" alt="{{ $offer->prestataire->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $offer->prestataire->user->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $offer->clientRequest->title }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <!-- Prix supprimé pour des raisons de confidentialité -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($offer->status === 'pending') bg-orange-100 text-orange-800
                                            @elseif($offer->status === 'accepted') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            @if($offer->status === 'pending') En attente
                                            @elseif($offer->status === 'accepted') Acceptée
                                            @else Refusée
                                            @endif
                                        </span>
                                        <span class="text-sm text-gray-500">{{ $offer->created_at->format('d/m/Y') }}</span>
                                        <a href="{{ route('client.requests.show', $offer->clientRequest->id) }}" class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                @if($offer->message)
                                    <div class="mt-3 pl-14">
                                        <p class="text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                            "{{ Str::limit($offer->message, 100) }}"
                                        </p>
                                    </div>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $offers->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune offre reçue</h3>
                        <p class="text-gray-600 mb-4">Vous n'avez pas encore reçu d'offres pour vos demandes de service.</p>
                        <a href="{{ route('client.requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Créer une demande
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
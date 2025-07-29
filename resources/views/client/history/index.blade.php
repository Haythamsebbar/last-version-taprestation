@extends('layouts.app')

@section('title', 'Mon historique')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6 sm:py-8">
        <!-- BLOC PRINCIPAL: En-tête -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-300 p-4 sm:p-6 mb-6 sm:mb-8 card-hover">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Mon historique
                    </h1>
                    <p class="text-gray-600 mt-1 sm:mt-2">Consultez l'historique de toutes vos activités</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('client.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour au tableau de bord
                    </a>
                </div>
            </div>
        </div>

        <!-- SECTION: STATISTIQUES DE L'HISTORIQUE -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Aperçu de votre activité
            </h2>
            
            <!-- Statistiques principales avec barres de progression -->            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center justify-between mb-3">
                        <div class="rounded-full bg-gradient-to-r from-purple-500 to-purple-600 p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
                            <p class="text-xs text-gray-500">Total</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Réservations totales</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full" style="width: {{ $stats['total_bookings'] > 0 ? min(100, ($stats['total_bookings'] / 10) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center justify-between mb-3">
                        <div class="rounded-full bg-gradient-to-r from-green-500 to-green-600 p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_bookings'] }}</p>
                            <p class="text-xs text-gray-500">Terminées</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Réservations terminées</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: {{ $stats['completed_bookings'] > 0 ? min(100, ($stats['completed_bookings'] / 8) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center justify-between mb-3">
                        <div class="rounded-full bg-gradient-to-r from-blue-500 to-blue-600 p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_requests'] }}</p>
                            <p class="text-xs text-gray-500">Créées</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Demandes créées</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" style="width: {{ $stats['total_requests'] > 0 ? min(100, ($stats['total_requests'] / 10) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center justify-between mb-3">
                        <div class="rounded-full bg-gradient-to-r from-yellow-500 to-orange-500 p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_rating_given'], 1) }}</p>
                            <p class="text-xs text-gray-500">/5</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Note moyenne donnée</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 h-2 rounded-full" style="width: {{ ($stats['average_rating_given'] / 5) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION: FILTRES -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-300 p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtres de recherche
                </h2>
                <p class="text-gray-600 text-sm mt-1">Affinez votre historique selon vos critères</p>
            </div>
            
            <form method="GET" action="{{ route('client.history.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1 min-w-0">
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                    <select name="period" id="period" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-300">
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Toute la période</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Dernier mois</option>
                        <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>3 derniers mois</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Dernière année</option>
                    </select>
                </div>
                
                <div class="flex-1 min-w-0">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type d'activité</label>
                    <select name="type" id="type" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-300">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Toutes les activités</option>
                        <option value="bookings" {{ $type === 'bookings' ? 'selected' : '' }}>Réservations</option>
                        <option value="requests" {{ $type === 'requests' ? 'selected' : '' }}>Demandes</option>
                        <option value="reviews" {{ $type === 'reviews' ? 'selected' : '' }}>Avis donnés</option>
                        <option value="messages" {{ $type === 'messages' ? 'selected' : '' }}>Messages</option>
                    </select>
                </div>
                
                <div class="flex-shrink-0">
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-md hover:shadow-lg flex items-center font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- CONTENU PRINCIPAL -->
        <div class="flex flex-col lg:grid lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- HISTORIQUE DES ACTIVITÉS -->
            <div class="order-1 lg:order-1 lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                    <div class="p-4 sm:p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Historique des activités
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Consultez toutes vos interactions passées</p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <!-- Réservations -->
                        @if($type === 'all' || $type === 'bookings')
                            @foreach($bookings as $booking)
                                <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3 sm:space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    Réservation - {{ $booking->service->nom }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    avec {{ $booking->prestataire->user->prenom }} {{ $booking->prestataire->user->nom }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $booking->created_at->format('d/m/Y à H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($booking->status === 'completed') bg-green-100 text-green-800
                                            @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <!-- Demandes -->
                        @if($type === 'all' || $type === 'requests')
                            @foreach($requests as $request)
                                <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3 sm:space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    Demande - {{ $request->titre }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ Str::limit($request->description, 100) }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $request->created_at->format('d/m/Y à H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($request->status === 'completed') bg-green-100 text-green-800
                                            @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                            @elseif($request->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <!-- Avis -->
                        @if($type === 'all' || $type === 'reviews')
                            @foreach($reviews as $review)
                                <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-start space-x-3 sm:space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                Avis donné - {{ $review->rating }}/5 étoiles
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                pour {{ $review->prestataire->user->prenom }} {{ $review->prestataire->user->nom }}
                                            </p>
                                            @if($review->comment)
                                                <p class="text-sm text-gray-600 mt-1">
                                                    "{{ Str::limit($review->comment, 100) }}"
                                                </p>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $review->created_at->format('d/m/Y à H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <!-- Messages -->
                        @if($type === 'all' || $type === 'messages')
                            @foreach($messages as $message)
                                <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-start space-x-3 sm:space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                Message envoyé
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                à {{ $message->receiver->prenom }} {{ $message->receiver->nom }}
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                "{{ Str::limit($message->content, 100) }}"
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $message->created_at->format('d/m/Y à H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if($bookings->isEmpty() && $requests->isEmpty() && $reviews->isEmpty() && $messages->isEmpty())
                            <div class="p-8 sm:p-12 text-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Aucune activité trouvée</h3>
                                <p class="text-gray-500 mb-6">Aucune activité ne correspond aux filtres sélectionnés.</p>
                                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                    <a href="{{ route('client.requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Créer une demande
                                    </a>
                                    <a href="{{ route('client.browse.prestataires') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Parcourir les prestataires
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SIDEBAR -->
            <div class="order-2 lg:order-2 space-y-6 sm:space-y-8">
                <!-- ACTIVITÉ MENSUELLE -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-300 p-4 sm:p-6">
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Activité des 12 derniers mois
                        </h3>
                        <p class="text-gray-600 text-sm mt-1">Évolution de vos activités</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($monthlyActivity as $month)
                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <span class="text-gray-700 font-medium text-sm">{{ $month['month'] }}</span>
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center space-x-1">
                                        <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                        <span class="text-blue-600 font-semibold text-sm">{{ $month['bookings'] }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="w-2 h-2 bg-purple-600 rounded-full"></div>
                                        <span class="text-purple-600 font-semibold text-sm">{{ $month['requests'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Légende :</span>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                    <span class="text-xs text-gray-600">Réservations</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-purple-600 rounded-full"></div>
                                    <span class="text-xs text-gray-600">Demandes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTIONS RAPIDES -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-300 p-4 sm:p-6">
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Actions rapides
                        </h3>
                        <p class="text-gray-600 text-sm mt-1">Accès direct aux fonctionnalités</p>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('client.requests.create') }}" class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-md hover:shadow-lg font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nouvelle demande
                        </a>
                        <a href="{{ route('client.browse.prestataires') }}" class="flex items-center justify-center w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Parcourir les prestataires
                        </a>
                        <a href="{{ route('client.messaging.index') }}" class="flex items-center justify-center w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Mes messages
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
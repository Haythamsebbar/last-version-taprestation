@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-blue-100">
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8 fade-in-up">
    <!-- Welcome Message -->
    <div class="welcome-card card-hover mb-8 flex items-center justify-between bg-white rounded-xl shadow-lg border border-blue-200">
        <div class="flex-1">
            <div class="flex items-center mb-3">
                <div class="step-circle mr-4">
                    <i class="fas fa-home"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $welcomeMessage }}</h1>
                    <p class="text-lg text-gray-600">Heureux de vous revoir, {{ $client->user->name }} !</p>
                </div>
            </div>
            <div class="flex items-center text-sm text-gray-500">
                <i class="fas fa-calendar-alt mr-2"></i>
                <span>{{ now()->format('l d F Y') }}</span>
                <span class="mx-2">•</span>
                <i class="fas fa-clock mr-2"></i>
                <span>{{ now()->format('H:i') }}</span>
            </div>
        </div>
        <div class="ml-6">
            @if($client->avatar)
                <div class="relative">
                    <img src="{{ asset('storage/' . $client->avatar) }}" alt="Photo de profil" class="h-20 w-20 rounded-full object-cover border-4 border-white shadow-lg">
                    <div class="absolute -bottom-1 -right-1 h-6 w-6 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
            @else
                <div class="relative">
                    <div class="h-20 w-20 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center border-4 border-white shadow-lg">
                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($client->user->name, 0, 1)) }}</span>
                    </div>
                    <div class="absolute -bottom-1 -right-1 h-6 w-6 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
            @endif
        </div>
    </div>

    <!-- Shortcuts -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($shortcuts as $shortcut)
            <a href="{{ $shortcut['url'] }}" class="group bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center space-x-4 border border-blue-200 card-hover">
                <div class="icon-circle bg-gradient-to-r from-blue-500 to-purple-600 text-white group-hover:scale-110 transition-transform duration-300">
                    <i class="{{ $shortcut['icon'] }} text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors duration-300">{{ $shortcut['name'] }}</h3>
                    <p class="text-gray-500 text-sm mt-1">{{ $shortcut['description'] }}</p>
                    <div class="flex items-center mt-2 text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="text-xs font-medium">Accéder</span>
                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Unified Recent Requests -->
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="icon-circle bg-gradient-to-r from-indigo-500 to-purple-600 text-white mr-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Mes demandes récentes</h2>
                </div>
                <a href="{{ route('client.requests.all') }}" class="action-button bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center border border-blue-200">
                    Voir plus
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="bg-white rounded-xl shadow-lg border border-blue-200">
                @if($unifiedRequests->isEmpty())
                    <div class="empty-state p-10">
                        <div class="empty-state-icon bg-gradient-to-br from-blue-50 to-indigo-100 mb-6">
                            <i class="fas fa-clipboard-list text-3xl text-indigo-500"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-3">Aucune demande pour le moment</h3>
                        <p class="text-gray-500 mb-6 max-w-md mx-auto">Commencez votre parcours en explorant nos services disponibles et en créant vos premières demandes.</p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('client.prestataires.index') }}" class="action-button bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>
                                Rechercher des services
                         </a>
                            <a href="{{ route('client.equipment-rentals.index') }}" class="action-button bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-tools mr-2"></i>
                                Louer du matériel
                            </a>
                        </div>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100 custom-scrollbar max-h-96 overflow-y-auto">
                        @foreach($unifiedRequests as $request)
                            <li class="list-item p-5 hover:bg-blue-50 transition-all duration-200 border-l-4 border-transparent hover:border-blue-400">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <div class="icon-circle bg-gradient-to-r from-indigo-500 to-purple-600 text-white flex-shrink-0">
                                            @if($request['type'] === 'service')
                                                <i class="fas fa-concierge-bell text-sm"></i>
                                            @else
                                                <i class="fas fa-tools text-sm"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $request['badge_color'] }}">
                                                    {{ $request['badge_text'] }}
                                                </span>
                                            </div>
                                            <h4 class="font-semibold text-gray-900 mb-1 text-lg">{{ $request['title'] }}</h4>
                                            <p class="text-sm text-gray-600 mb-2 flex items-center">
                                                <i class="fas fa-user-tie mr-2 text-gray-400"></i>
                                                {{ $request['prestataire'] }}
                                            </p>
                                            <p class="text-sm text-gray-500 flex items-center">
                                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                                @if($request['type'] === 'service')
                                                    Réservé le {{ $request['date']->format('d/m/Y à H:i') }}
                                                @else
                                                    Demandé le {{ $request['date']->format('d/m/Y') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right flex flex-col items-end space-y-2">
                                        <span class="px-4 py-2 rounded-full text-sm font-semibold shadow-sm
                                            @if($request['status'] === 'pending') bg-gradient-to-r from-yellow-400 to-orange-500 text-white
                                            @elseif(in_array($request['status'], ['confirmed', 'approved', 'responded'])) bg-gradient-to-r from-green-400 to-green-600 text-white
                                            @elseif(in_array($request['status'], ['completed', 'active'])) bg-gradient-to-r from-blue-400 to-blue-600 text-white
                                            @elseif(in_array($request['status'], ['cancelled', 'rejected'])) bg-gradient-to-r from-red-400 to-red-600 text-white
                                            @else bg-gradient-to-r from-gray-400 to-gray-600 text-white @endif">
                                            @switch($request['status'])
                                                @case('pending')
                                                    <i class="fas fa-clock mr-1"></i> En attente
                                                    @break
                                                @case('confirmed')
                                                    <i class="fas fa-check-circle mr-1"></i> Confirmé
                                                    @break
                                                @case('approved')
                                                    <i class="fas fa-thumbs-up mr-1"></i> Approuvé
                                                    @break
                                                @case('completed')
                                                    <i class="fas fa-flag-checkered mr-1"></i> Terminé
                                                    @break
                                                @case('active')
                                                    <i class="fas fa-play-circle mr-1"></i> En cours
                                                    @break
                                                @case('cancelled')
                                                    <i class="fas fa-times-circle mr-1"></i> Annulé
                                                    @break
                                                @case('rejected')
                                                    <i class="fas fa-ban mr-1"></i> Rejeté
                                                    @break
                                                @case('responded')
                                                    <i class="fas fa-reply mr-1"></i> Répondu
                                                    @break
                                                @default
                                                    {{ ucfirst($request['status']) }}
                                            @endswitch
                                        </span>
                                        <button class="text-blue-500 hover:text-blue-700 text-sm font-medium flex items-center transition-colors duration-200">
                                            <span>Voir détails</span>
                                            <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Unread Messages -->
        <div>
            <div class="flex items-center mb-6">
                <div class="icon-circle bg-gradient-to-r from-blue-500 to-indigo-600 text-white mr-3">
                    <i class="fas fa-envelope"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Messages</h2>
            </div>
            <div class="stat-card bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-8 rounded-xl shadow-lg text-center border border-blue-200">
                <div class="relative mb-6">
                    <div class="icon-circle bg-gradient-to-r from-blue-500 to-indigo-600 text-white mx-auto" style="width: 80px; height: 80px;">
                        <i class="fas fa-envelope-open-text text-3xl"></i>
                    </div>
                    @if($unreadMessages > 0)
                        <div class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold animate-pulse">
                            {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
                        </div>
                    @endif
                </div>
                <div class="mb-6">
                    <p class="text-5xl font-bold text-indigo-800 mb-2">{{ $unreadMessages }}</p>
                    <p class="text-indigo-600 text-lg">
                        @if($unreadMessages == 0)
                            Aucun nouveau message
                        @elseif($unreadMessages == 1)
                            nouveau message
                        @else
                            nouveaux messages
                        @endif
                    </p>
                </div>
                <a href="{{ route('client.messaging.index') }}" class="action-button bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-3 rounded-full hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center">
                    <i class="fas fa-comments mr-2"></i>
                    Voir mes messages
                </a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
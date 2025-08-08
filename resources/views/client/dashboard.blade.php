@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
@endpush

@section('content')
<div class="min-h-screen bg-blue-50">
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8 fade-in-up">
    <!-- Welcome Message -->
    <div class="welcome-card card-hover mb-8 flex items-center justify-between bg-white rounded-xl shadow-lg border border-blue-200">
        <div class="flex-1">
            <div class="flex items-center mb-3">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-home text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-blue-900">{{ $welcomeMessage }}</h1>
                    <p class="text-lg text-blue-700">Heureux de vous revoir, {{ $client->user->name }} !</p>
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
                    <div class="h-20 w-20 rounded-full bg-blue-600 flex items-center justify-center border-4 border-white shadow-lg">
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
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                    <i class="{{ $shortcut['icon'] }} text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-blue-800 group-hover:text-blue-600 transition-colors duration-300">{{ $shortcut['name'] }}</h3>
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
        <div class="lg:col-span-2 space-y-8">
            <!-- Mes demandes récentes -->
            <div>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white mr-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-800">Mes demandes récentes</h2>
                </div>
                <a href="{{ route('client.requests.all') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold px-4 py-2 rounded-lg text-sm transition duration-200 flex items-center border border-blue-200">
                    Voir plus
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="bg-white rounded-xl shadow-lg border border-blue-200">
                @if($unifiedRequests->isEmpty())
                    <div class="empty-state p-10">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-clipboard-list text-3xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-3">Aucune demande pour le moment</h3>
                        <p class="text-gray-500 mb-6 max-w-md mx-auto">Commencez votre parcours en explorant nos services disponibles et en créant vos premières demandes.</p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('client.prestataires.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>
                                Rechercher des services
                         </a>
                            <a href="{{ route('client.equipment-rentals.index') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold px-6 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                                <i class="fas fa-tools mr-2"></i>
                                Louer du matériel
                            </a>
                        </div>
                    </div>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach($unifiedRequests as $request)
                            <a href="@if($request['type'] === 'service'){{ route('client.bookings.show', $request['id']) }}@elseif($request['type'] === 'equipment'){{ route('client.equipment-rental-requests.show', $request['id']) }}@else{{ route('urgent-sales.show', $request['id']) }}@endif" class="block py-4 px-6 hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                                <div class="flex items-center gap-4">
                                    <!-- Image du service/équipement -->
                                    <div class="flex-shrink-0">
                                        @if($request['type'] === 'service' && isset($request['image']) && $request['image'])
                                            <img src="{{ asset('storage/' . $request['image']) }}" alt="{{ $request['title'] }}" class="h-16 w-16 rounded-lg object-cover">
                                        @elseif($request['type'] === 'equipment' && isset($request['image']) && $request['image'])
                                            <img src="{{ asset('storage/' . $request['image']) }}" alt="{{ $request['title'] }}" class="h-16 w-16 rounded-lg object-cover">
                                        @else
                                            <div class="h-16 w-16 rounded-lg flex items-center justify-center
                                                @if($request['type'] === 'service') bg-blue-100 text-blue-600
                                                @else bg-green-100 text-green-600 @endif">
                                                @if($request['type'] === 'service')
                                                    <i class="fas fa-cogs text-2xl"></i>
                                                @else
                                                    <i class="fas fa-tools text-2xl"></i>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Contenu principal -->
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="px-2 py-1 rounded text-xs font-medium
                                                @if($request['type'] === 'service') bg-blue-100 text-blue-800
                                                @else bg-green-100 text-green-800 @endif">
                                                @if($request['type'] === 'service') Service @else Matériel @endif
                                            </span>
                                            <h4 class="font-semibold text-gray-900">{{ $request['title'] }}</h4>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            <span>{{ $request['prestataire'] }}</span>
                                            <span>
                                                @if($request['type'] === 'service')
                                                    {{ $request['date']->format('d/m/Y à H:i') }}
                                                @else
                                                    {{ $request['date']->format('d/m/Y') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Statut -->
                                    <div class="flex-shrink-0">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($request['status'] === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif(in_array($request['status'], ['confirmed', 'approved', 'responded'])) bg-green-100 text-green-800
                                            @elseif(in_array($request['status'], ['completed', 'active'])) bg-blue-100 text-blue-800
                                            @elseif(in_array($request['status'], ['cancelled', 'rejected'])) bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            @switch($request['status'])
                                                @case('pending')
                                                    En attente
                                                    @break
                                                @case('confirmed')
                                                    Confirmé
                                                    @break
                                                @case('approved')
                                                    Approuvé
                                                    @break
                                                @case('completed')
                                                    Terminé
                                                    @break
                                                @case('active')
                                                    En cours
                                                    @break
                                                @case('cancelled')
                                                    Annulé
                                                    @break
                                                @case('rejected')
                                                    Rejeté
                                                    @break
                                                @case('responded')
                                                    Répondu
                                                    @break
                                                @default
                                                    {{ ucfirst($request['status']) }}
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <br>
            <br>
            <!-- Mes abonnements -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white mr-3">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-blue-800">Mes abonnements</h2>
                    </div>
                    <a href="{{ route('client.prestataire-follows.index') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold px-4 py-2 rounded-lg text-sm transition duration-200 flex items-center">
                        Voir tous
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="bg-white rounded-xl shadow-lg border border-blue-200">
                    @if($recentFollowedPrestataires->isEmpty())
                        <div class="empty-state p-10">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-heart text-3xl text-blue-600"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-3">Aucun abonnement pour le moment</h3>
                            <p class="text-gray-500 mb-6 max-w-md mx-auto">Découvrez et suivez vos prestataires préférés pour rester informé de leurs dernières activités.</p>
                            <a href="{{ route('client.prestataires.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>
                                Découvrir des prestataires
                            </a>
                        </div>
                    @else
                        <div class="divide-y divide-gray-200">
                            @foreach($recentFollowedPrestataires as $prestataire)
                                <div class="py-4 px-6 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4 flex-1">
                                            <div class="flex-shrink-0">
                                                @if($prestataire->photo)
                                                    <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="{{ $prestataire->user->name }}" class="h-10 w-10 rounded-full object-cover">
                                                @elseif($prestataire->user->avatar)
                                                    <img src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="{{ $prestataire->user->name }}" class="h-10 w-10 rounded-full object-cover">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
                                                        <span class="text-sm font-bold text-white">{{ strtoupper(substr($prestataire->user->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="font-semibold text-gray-900">{{ $prestataire->user->name }}</h4>
                                                    @if($prestataire->is_approved)
                                                        <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            Vérifié
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 mb-2">{{ $prestataire->company_name }}</p>
                                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                                    @if($prestataire->services->count() > 0)
                                                        <span>{{ $prestataire->services->count() }} service(s)</span>
                                                    @endif
                                                    @if($prestataire->rating_average > 0)
                                                        <span>★ {{ number_format($prestataire->rating_average, 1) }}</span>
                                                    @endif
                                                    <span>Suivi depuis {{ $prestataire->pivot->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('prestataires.show', $prestataire->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200">
                                                Voir profil
                                            </a>
                                            <form action="{{ route('client.prestataire-follows.unfollow', $prestataire->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-200" onclick="return confirm('Êtes-vous sûr de vouloir vous désabonner ?')">
                                                    Se désabonner
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    @endif
                </div>
            </div>
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
            <br>
            <br>
            <!-- Dernières activités -->
        @if($recentServicesFromFollowed->isNotEmpty())
        <div>
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center text-white mr-3">
                    <i class="fas fa-bell"></i>
                </div>
                <h2 class="text-2xl font-bold text-purple-800">Dernières activités</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg border border-purple-200">
                <div class="divide-y divide-gray-200">
                    @foreach($recentServicesFromFollowed as $service)
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    @if($service->prestataire->photo)
                                        <img src="{{ asset('storage/' . $service->prestataire->photo) }}" alt="{{ $service->prestataire->user->name }}" class="h-10 w-10 rounded-full object-cover">
                                    @elseif($service->prestataire->user->avatar)
                                        <img src="{{ asset('storage/' . $service->prestataire->user->avatar) }}" alt="{{ $service->prestataire->user->name }}" class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                            {{ substr($service->prestataire->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-gray-900">{{ $service->prestataire->user->name }}</span>
                                        <span class="text-sm text-gray-500">a ajouté un nouveau service</span>
                                    </div>
                                    <h4 class="font-semibold text-purple-800 mb-1">{{ $service->title }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($service->description, 100) }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">{{ $service->created_at->diffForHumans() }}</span>
                                        <a href="{{ route('services.show', $service->id) }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium transition-colors duration-200">
                                            Voir le service
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        </div>

        
    </div>
</div>
</div>
@endsection
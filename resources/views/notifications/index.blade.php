@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de la page -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bell text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Mes notifications</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            @if($notifications->count() > 0)
                                {{ $notifications->total() }} notification{{ $notifications->total() > 1 ? 's' : '' }} au total
                            @else
                                Restez informé de toutes vos activités
                            @endif
                        </p>
                    </div>
                </div>
                
                @if($notifications->where('read_at', null)->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-check-double mr-2 text-xs"></i>
                            Tout marquer comme lu
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Messages de feedback -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Contenu principal -->
        @if($notifications->count() > 0)
            <!-- Liste des notifications -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="divide-y divide-gray-100">
                    @foreach($notifications as $notification)
                        <div class="p-6 hover:bg-gray-50 transition-colors duration-200 {{ !$notification->read_at ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <!-- Icône de notification -->
                                    <div class="flex-shrink-0">
                                        @php
                                            $iconConfig = [
                                                'App\\Notifications\\NewOfferNotification' => ['icon' => 'fa-handshake', 'color' => 'text-blue-500', 'bg' => 'bg-blue-100'],
                                                'App\\Notifications\\OfferAcceptedNotification' => ['icon' => 'fa-check-circle', 'color' => 'text-green-500', 'bg' => 'bg-green-100'],
                                                'App\\Notifications\\OfferRejectedNotification' => ['icon' => 'fa-times-circle', 'color' => 'text-red-500', 'bg' => 'bg-red-100'],
                                                'App\\Notifications\\BookingCancelledNotification' => ['icon' => 'fa-calendar-times', 'color' => 'text-orange-500', 'bg' => 'bg-orange-100'],
                                                'App\\Notifications\\MissionCompletedNotification' => ['icon' => 'fa-trophy', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-100'],
                                                'App\\Notifications\\NewReviewNotification' => ['icon' => 'fa-star', 'color' => 'text-purple-500', 'bg' => 'bg-purple-100'],
                                                'App\\Notifications\\PrestataireApprovedNotification' => ['icon' => 'fa-user-check', 'color' => 'text-green-500', 'bg' => 'bg-green-100'],
                                                'App\\Notifications\\RequestHasOffersNotification' => ['icon' => 'fa-envelope', 'color' => 'text-blue-500', 'bg' => 'bg-blue-100'],
                                                'App\\Notifications\\NewMessageNotification' => ['icon' => 'fa-comment', 'color' => 'text-purple-500', 'bg' => 'bg-purple-100'],
                                                'App\\Notifications\\NewClientRequestNotification' => ['icon' => 'fa-clipboard-list', 'color' => 'text-orange-500', 'bg' => 'bg-orange-100'],
                                                'App\\Notifications\\AnnouncementStatusNotification' => ['icon' => 'fa-bullhorn', 'color' => 'text-indigo-500', 'bg' => 'bg-indigo-100'],
                                            ];
                                            $config = $iconConfig[$notification->type] ?? ['icon' => 'fa-bell', 'color' => 'text-gray-500', 'bg' => 'bg-gray-100'];
                                        @endphp
                                        <div class="w-10 h-10 {{ $config['bg'] }} rounded-lg flex items-center justify-center">
                                            <i class="fas {{ $config['icon'] }} {{ $config['color'] }}"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Contenu de la notification -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            @if(!$notification->read_at)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1"></span>
                                                    Nouveau
                                                </span>
                                            @endif
                                            <h3 class="text-sm font-semibold text-gray-900 truncate">
                                                @php
                                                    $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                                                @endphp
                                                {{ $data['title'] ?? 'Notification' }}
                                            </h3>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2 leading-relaxed">
                                            {{ $data['message'] ?? '' }}
                                        </p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center space-x-2 ml-4">
                                    @if(!$notification->read_at)
                                        <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                <i class="fas fa-check mr-1"></i>
                                                Marquer comme lu
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')">
                                            <i class="fas fa-trash mr-1"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            @endif
        @else
            <!-- État vide -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="max-w-md mx-auto">
                    <!-- Illustration -->
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-bell-slash text-blue-500 text-2xl"></i>
                    </div>
                    
                    <!-- Message principal -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Vous êtes à jour !
                    </h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Aucune nouvelle notification pour le moment. Nous vous tiendrons informé de toutes vos activités importantes.
                    </p>
                    
                    <!-- Actions suggérées -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        @if(Auth::user()->hasRole('client'))
                            <a href="{{ route('client.requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-plus mr-2"></i>
                                Publier une demande
                            </a>
                        @elseif(Auth::user()->hasRole('prestataire'))
                            <a href="{{ route('prestataire.responses.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-search mr-2"></i>
                                Voir les demandes
                            </a>
                        @endif
                        <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <i class="fas fa-home mr-2"></i>
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
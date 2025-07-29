@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/messaging.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/messaging.js') }}" defer></script>
@endpush

@section('content')
<div class="py-6" data-current-user-id="{{ Auth::id() }}">
    <header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">
                        <i class="fas fa-comments text-indigo-600 mr-3"></i>
                        Messagerie
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Communiquez avec vos prestataires ou clients en temps réel</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-circle text-green-500 mr-1"></i>
                        En ligne
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <main>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="messaging-container">
                    <div class="px-6 py-4 border-b border-gray-200 bg-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Mes conversations</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $conversations->count() }} conversation{{ $conversations->count() > 1 ? 's' : '' }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="location.reload()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="conversations-list">
                        @if($conversations->count() > 0)
                            @foreach($conversations as $conversation)
                                <a href="{{ route('messaging.conversation', $conversation['user']->id) }}" 
                                   class="conversation-item {{ $conversation['unread_count'] > 0 ? 'unread' : '' }}"
                                   data-user-id="{{ $conversation['user']->id }}">
                                    <div class="conversation-avatar">
                                        @if($conversation['user']->profile_photo_url)
                                            <img src="{{ $conversation['user']->profile_photo_url }}" 
                                                 alt="{{ $conversation['user']->name }}" 
                                                 class="avatar-image">
                                        @else
                                            <div class="avatar-placeholder">
                                                {{ strtoupper(substr($conversation['user']->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        @if($conversation['user']->is_online ?? false)
                                            <div class="online-indicator"></div>
                                        @endif
                                    </div>
                                    
                                    <div class="conversation-content">
                                        <div class="conversation-header">
                                            <h4 class="conversation-name">{{ $conversation['user']->name }}</h4>
                                            <div class="conversation-meta">
                                                @if($conversation['last_message'])
                                                    <span class="conversation-time">
                                                        {{ $conversation['last_message']->created_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                                @if($conversation['unread_count'] > 0)
                                                    <span class="unread-badge">{{ $conversation['unread_count'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="conversation-preview">
                                            <div class="user-role">
                                                @if($conversation['user']->role === 'prestataire')
                                                    <i class="fas fa-tools text-blue-500 mr-1"></i>
                                                    <span class="role-text">Prestataire</span>
                                                @else
                                                    <i class="fas fa-user text-green-500 mr-1"></i>
                                                    <span class="role-text">Client</span>
                                                @endif
                                            </div>
                                            
                                            @if($conversation['last_message'])
                                                <p class="last-message">
                                                    @if($conversation['last_message']->sender_id === Auth::id())
                                                        <i class="fas fa-reply text-gray-400 mr-1"></i>
                                                    @endif
                                                    {{ Str::limit($conversation['last_message']->content, 60) }}
                                                </p>
                                            @else
                                                <p class="last-message no-messages">
                                                    <i class="fas fa-comment-dots text-gray-400 mr-1"></i>
                                                    Commencer la conversation
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <h3 class="empty-title">Aucune conversation</h3>
                                <p class="empty-description">
                                    Vous n'avez pas encore de conversations. Commencez à échanger avec des 
                                    {{ Auth::user()->role === 'client' ? 'prestataires' : 'clients' }} pour voir vos messages ici.
                                </p>
                                @if(Auth::user()->role === 'client')
                                    <div class="empty-action">
                                        <a href="{{ route('prestataires.index') }}" class="btn-primary">
                                            <i class="fas fa-search mr-2"></i>
                                            Trouver des prestataires
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Interface de messagerie (à implémenter) -->
            <div class="px-4 py-6 sm:px-0 hidden">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-4">
                        <!-- Liste des conversations -->
                        <div class="md:col-span-1 border-r border-gray-200">
                            <div class="px-4 py-5 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Conversations</h3>
                            </div>
                            <div class="border-t border-gray-200 divide-y divide-gray-200">
                                <!-- Exemple de conversation -->
                                <div class="p-4 hover:bg-gray-50 cursor-pointer">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gray-300"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">Nom du contact</p>
                                            <p class="text-sm text-gray-500 truncate">Dernier message...</p>
                                        </div>
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">2 nouveaux</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Conversation active -->
                        <div class="md:col-span-3 flex flex-col h-[600px]">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-300"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Nom du contact</p>
                                        <p class="text-xs text-gray-500">En ligne</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Messages -->
                            <div class="flex-1 p-4 overflow-y-auto bg-gray-50">
                                <div class="space-y-4">
                                    <!-- Message reçu -->
                                    <div class="flex">
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="h-8 w-8 rounded-full bg-gray-300"></div>
                                        </div>
                                        <div>
                                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                                <p class="text-sm">Bonjour, je suis intéressé par vos services.</p>
                                            </div>
                                            <span class="text-xs text-gray-500 mt-1">10:23</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Message envoyé -->
                                    <div class="flex justify-end">
                                        <div>
                                            <div class="bg-indigo-100 p-3 rounded-lg shadow-sm">
                                                <p class="text-sm">Bonjour ! Je serais ravi de discuter de vos besoins.</p>
                                            </div>
                                            <span class="text-xs text-gray-500 mt-1 block text-right">10:25</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Formulaire d'envoi -->
                            <div class="p-4 border-t border-gray-200">
                                <form class="flex space-x-2">
                                    <input type="text" placeholder="Écrivez votre message..." class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Envoyer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le système de messagerie
        if (typeof MessagingSystem !== 'undefined') {
            window.messagingSystem = new MessagingSystem();
        }
    });
</script>
@endsection
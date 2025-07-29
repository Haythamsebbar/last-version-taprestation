@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/messaging.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/messaging.js') }}" defer></script>
@endpush

@section('content')
<div class="py-6" data-current-user-id="{{ Auth::id() }}" data-conversation-user-id="{{ $user->id }}">
    <header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="messaging-container">
                <div class="conversation-header">
                    <div class="flex items-center">
                        <a href="{{ route('messaging.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        
                        <div class="conversation-avatar mr-4">
                            @if($user->client && $user->client->photo)
                                <img src="{{ asset('storage/' . $user->client->photo) }}" 
                                     alt="{{ $user->name }}" 
                                     class="avatar-image">
                            @elseif($user->prestataire && $user->prestataire->photo)
                                <img src="{{ asset('storage/' . $user->prestataire->photo) }}" 
                                     alt="{{ $user->name }}" 
                                     class="avatar-image">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="online-indicator {{ ($user->is_online ?? false) ? 'online' : 'offline' }}"></div>
                        </div>
                        
                        <div class="conversation-info">
                            <h1 class="conversation-title">{{ $user->name }}</h1>
                            <div class="conversation-status">
                                @if($user->role === 'prestataire')
                                    <i class="fas fa-tools text-blue-500 mr-1"></i>
                                    <span>Prestataire</span>
                                @else
                                    <i class="fas fa-user text-green-500 mr-1"></i>
                                    <span>Client</span>
                                @endif
                                <span class="status-separator">•</span>
                                <span class="online-status" id="user-online-status">
                                    @if($user->is_online ?? false)
                                        <i class="fas fa-circle text-green-500 mr-1"></i>
                                        En ligne
                                    @else
                                        <i class="fas fa-circle text-gray-400 mr-1"></i>
                                        {{ $user->online_status ?? 'Hors ligne' }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="conversation-actions">
                        <button class="action-button" title="Actualiser">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button class="action-button" title="Plus d'options">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <main>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="messaging-container">
                    <!-- Zone des messages -->
                    <div class="messages-container" id="messages-container">
                        <div class="messages-list" id="messages-list">
                            @forelse($messages as $message)
                                <div class="message {{ $message->sender_id === Auth::id() ? 'sent' : 'received' }}" 
                                     data-message-id="{{ $message->id }}">
                                    <div class="message-bubble">
                                        <div class="message-content">
                                            {{ $message->content }}
                                        </div>
                                        <div class="message-meta">
                                            <span class="message-time">
                                                {{ $message->created_at->format('H:i') }}
                                            </span>
                                            @if($message->sender_id === Auth::id())
                                                <span class="message-status" data-message-id="{{ $message->id }}">
                                                    @if($message->read_at)
                                                        <i class="fas fa-check-double text-blue-500" title="Lu le {{ $message->read_at->format('d/m/Y à H:i') }}"></i>
                                                    @else
                                                        <i class="fas fa-check text-gray-400" title="Envoyé"></i>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-conversation">
                                    <div class="empty-icon">
                                        <i class="fas fa-comment-dots"></i>
                                    </div>
                                    <h3 class="empty-title">Commencez la conversation</h3>
                                    <p class="empty-description">
                                        Envoyez votre premier message à {{ $user->name }} pour démarrer la discussion.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Formulaire d'envoi de message -->
                    <div class="message-input-container">
                        <form id="message-form" class="message-form">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                            
                            <div class="message-input-wrapper">
                                <div class="message-input-actions">
                                    <button type="button" class="attachment-button" title="Joindre un fichier">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                </div>
                                
                                <div class="message-input-field">
                                    <textarea 
                                        name="content" 
                                        id="message-input" 
                                        placeholder="Tapez votre message..." 
                                        rows="1"
                                        required
                                        maxlength="1000"></textarea>
                                </div>
                                
                                <div class="message-send-actions">
                                    <button type="button" class="emoji-button" title="Émojis">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                    <button type="submit" class="send-button" id="send-button">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="message-input-footer">
                                <div class="typing-indicator" id="typing-indicator" style="display: none;">
                                    <span>{{ $user->name }} est en train d'écrire...</span>
                                </div>
                                <div class="character-count">
                                    <span id="char-count">0</span>/1000
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le système de messagerie pour la conversation
        if (typeof MessagingSystem !== 'undefined') {
            window.messagingSystem = new MessagingSystem();
            
            // Faire défiler vers le bas au chargement
            const messagesContainer = document.getElementById('messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            
            // Compteur de caractères
            const messageInput = document.getElementById('message-input');
            const charCount = document.getElementById('char-count');
            
            if (messageInput && charCount) {
                messageInput.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
                
                // Auto-resize du textarea
                messageInput.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                });
            }
        }
    });
</script>
@endsection
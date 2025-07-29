@extends('layouts.app')

@section('content')
<div class="py-10">
    <header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold leading-tight text-gray-900">Messagerie</h1>
            <p class="mt-2 text-sm text-gray-600">Gérez vos conversations avec les prestataires</p>
        </div>
    </header>
    <main>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Statistiques rapides -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Conversations actives</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $conversations->count() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Messages non lus</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $totalUnreadCount }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Liste des conversations -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Conversations</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Vos échanges avec les prestataires</p>
                    </div>
                    
                    @if($conversations->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($conversations as $conversation)
                                @php
                                    $otherUser = $conversation['user'];
                                    $lastMessage = $conversation['last_message'];
                                    $unreadCount = $conversation['unread_count'];
                                @endphp
                                <li>
                                    <a href="{{ route('client.messaging.show', $otherUser->id) }}" class="block hover:bg-gray-50">
                                        <div class="px-4 py-4 flex items-center justify-between">
                                            <div class="flex items-center">
                                                @if($otherUser->prestataire && $otherUser->prestataire->photo)
                                                    <img class="h-10 w-10 rounded-full" src="{{ Storage::url($otherUser->prestataire->photo) }}" alt="{{ $otherUser->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">{{ substr($otherUser->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="flex items-center">
                                                        <p class="text-sm font-medium text-gray-900">{{ $otherUser->name }}</p>
                                                        @if($unreadCount > 0)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                {{ $unreadCount }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($lastMessage)
                                                        <p class="text-sm text-gray-600 truncate max-w-md">
                                                            @if($lastMessage->clientRequest)
                                                                <span class="text-indigo-600">[{{ $lastMessage->clientRequest->title }}]</span>
                                                            @endif
                                                            {{ Str::limit($lastMessage->content, 60) }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">{{ $lastMessage->created_at->diffForHumans() }}</p>
                                                    @else
                                                        <p class="text-sm text-gray-500">Aucun message</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                @if($lastMessage && $lastMessage->sender_id == auth()->id() && $lastMessage->read_at)
                                                    <span class="text-green-500 text-xs">✓ Lu</span>
                                                @elseif($lastMessage && $lastMessage->sender_id == auth()->id())
                                                    <span class="text-gray-400 text-xs">✓ Envoyé</span>
                                                @endif
                                                <svg class="ml-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune conversation</h3>
                            <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de conversations avec des prestataires.</p>
                            <div class="mt-6">
                                <a href="{{ route('client.requests.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Créer une demande
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
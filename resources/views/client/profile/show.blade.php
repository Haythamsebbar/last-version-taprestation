@extends('layouts.app')

@section('content')
<div class="py-10">
    <main>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- En-tête du profil -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if($client->avatar)
                                    <img class="h-20 w-20 rounded-full" src="{{ Storage::url($client->avatar) }}" alt="{{ $client->user->name }}">
                                @else
                                    <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-2xl font-medium text-gray-700">{{ substr($client->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="ml-6">
                                    <h1 class="text-3xl font-bold text-gray-900">{{ $client->user->name }}</h1>
                                    <p class="text-lg text-gray-600">Client</p>
                                    @if($client->location)
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            {{ $client->location }}
                                        </p>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Membre depuis {{ $client->user->created_at->format('F Y') }}
                                    </p>
                                </div>
                            </div>
                            @auth
                                @if(auth()->id() === $client->user_id)
                                    <div class="flex space-x-3">
                                        <a href="{{ route('client.profile.edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-edit mr-2"></i>
                                            Modifier le profil
                                        </a>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Colonne principale -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Présentation -->
                        @if($client->bio)
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Présentation</h3>
                                    <p class="text-gray-700 leading-relaxed">{{ $client->bio }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Demandes récentes -->
                        @if($recentRequests->count() > 0)
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Demandes récentes</h3>
                                    <div class="space-y-4">
                                        @foreach($recentRequests as $request)
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <h4 class="text-md font-medium text-gray-900">{{ $request->title }}</h4>
                                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($request->description, 120) }}</p>
                                                        <div class="flex items-center mt-2 space-x-4">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <!-- Budget supprimé pour des raisons de confidentialité -->
                                                            </span>
                                                            @if($request->category)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    {{ $request->category()->exists() ? $request->category->name : 'Non catégorisé' }}
                                                                </span>
                                                            @endif
                                                            <span class="text-xs text-gray-500">
                                                                {{ $request->created_at->diffForHumans() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($request->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                                            {{ ucfirst($request->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Avis reçus -->
                        @if($reviews->count() > 0)
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Avis reçus</h3>
                                    <div class="space-y-4">
                                        @foreach($reviews as $review)
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-start">
                                                    @if($review->prestataire->photo)
                                                        <img class="h-10 w-10 rounded-full" src="{{ Storage::url($review->prestataire->photo) }}" alt="{{ $review->prestataire->user->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">{{ substr($review->prestataire->user->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="ml-4 flex-1">
                                                        <div class="flex items-center justify-between">
                                                            <h4 class="text-sm font-medium text-gray-900">{{ $review->prestataire->user->name }}</h4>
                                                            <div class="flex items-center">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                    </svg>
                                                                @endfor
                                                                <span class="ml-1 text-sm text-gray-600">{{ $review->rating }}/5</span>
                                                            </div>
                                                        </div>
                                                        <p class="text-sm text-gray-700 mt-1">{{ $review->comment }}</p>
                                                        <p class="text-xs text-gray-500 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Statistiques -->
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Statistiques</h3>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Demandes publiées</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total_requests'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Projets terminés</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">{{ $stats['completed_requests'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Note moyenne</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">
                                            @if($stats['average_rating'])
                                                {{ number_format($stats['average_rating'], 1) }}/5
                                                <div class="flex items-center mt-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="h-4 w-4 {{ $i <= round($stats['average_rating']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                            @else
                                                <span class="text-gray-500">Aucun avis</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Prestataires suivis</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">{{ $stats['following_count'] }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        
                        <!-- Contact -->
                        @auth
                            @if(auth()->user()->role === 'prestataire' && auth()->id() !== $client->user_id)
                                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                    <div class="px-4 py-5 sm:p-6">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Contact</h3>
                                        <div class="space-y-3">
                                            <a href="{{ route('prestataire.messaging.start-conversation-from-request', $client->user_id) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <i class="fas fa-envelope mr-2"></i>
                                                Envoyer un message
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endauth
                        
                        <!-- Informations de contact -->
                        @if($client->phone || $client->location)
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations</h3>
                                    <dl class="space-y-3">
                                        @if($client->location)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">Localisation</dt>
                                                <dd class="text-sm text-gray-900 mt-1">
                                                    <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                                    {{ $client->location }}
                                                </dd>
                                            </div>
                                        @endif
                                        @if($client->phone && (auth()->check() && auth()->user()->role === 'prestataire'))
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                                <dd class="text-sm text-gray-900 mt-1">
                                                    <i class="fas fa-phone mr-1 text-gray-400"></i>
                                                    {{ $client->phone }}
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
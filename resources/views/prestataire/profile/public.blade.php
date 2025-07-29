@extends('layouts.app')

@section('title', $prestataire->user->name . ' - Profil Public')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header du profil -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-32"></div>
            <div class="relative px-6 pb-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-6">
                    <div class="-mt-16 relative">
                        @if($prestataire->user->profile_photo)
                            <img class="h-32 w-32 rounded-full border-4 border-white shadow-lg object-cover" 
                                 src="{{ Storage::url($prestataire->user->profile_photo) }}" 
                                 alt="{{ $prestataire->user->name }}">
                        @else
                            <div class="h-32 w-32 rounded-full border-4 border-white shadow-lg bg-gray-300 flex items-center justify-center">
                                <span class="text-4xl font-bold text-gray-600">
                                    {{ strtoupper(substr($prestataire->user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 sm:mt-0 flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $prestataire->user->name }}</h1>
                        <p class="text-lg text-gray-600">{{ $prestataire->sector }}</p>
                        @if($prestataire->service_area)
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $prestataire->service_area }}
                            </p>
                        @endif
                        <div class="flex items-center mt-2">
                            @if($stats['average_rating'] > 0)
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $stats['average_rating'])
                                            <i class="fas fa-star text-yellow-400"></i>
                                        @else
                                            <i class="far fa-star text-gray-300"></i>
                                        @endif
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600">
                                        {{ $stats['average_rating'] }} ({{ $stats['total_reviews'] }} avis)
                                    </span>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Aucun avis pour le moment</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Description -->
                @if($prestataire->description)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">À propos</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $prestataire->description }}</p>
                    </div>
                @endif

                <!-- Compétences -->
                @if($prestataire->skills->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Compétences</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($prestataire->skills as $skill)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $skill->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Services -->
                @if($services->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Services proposés</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($services as $service)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <h3 class="font-semibold text-gray-900 mb-2">{{ $service->title }}</h3>
                                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($service->description, 100) }}</p>
                                    <div class="flex justify-between items-center">
                                        <!-- Prix supprimé -->
                                        <span class="text-sm text-gray-500">{{ $service->duration }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($services->hasPages())
                            <div class="mt-6">
                                {{ $services->links() }}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Avis -->
                @if($reviews->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Avis clients</h2>
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                                <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            @if($review->client && $review->client->profile_photo)
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                     src="{{ Storage::url($review->client->profile_photo) }}" 
                                                     alt="{{ $review->client_name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-600">
                                                        {{ strtoupper(substr($review->client_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-medium text-gray-900">{{ $review->client_name }}</h4>
                                                <time class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</time>
                                            </div>
                                            <div class="flex items-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                                                    @else
                                                        <i class="far fa-star text-gray-300 text-sm"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            @if($review->comment)
                                                <p class="mt-2 text-gray-700">{{ $review->comment }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($reviews->hasPages())
                            <div class="mt-6">
                                {{ $reviews->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statistiques -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Services actifs</span>
                            <span class="font-semibold">{{ $stats['total_services'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Avis reçus</span>
                            <span class="font-semibold">{{ $stats['total_reviews'] }}</span>
                        </div>
                        @if($stats['average_rating'] > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Note moyenne</span>
                                <span class="font-semibold">{{ $stats['average_rating'] }}/5</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Membre depuis</span>
                            <span class="font-semibold">{{ $stats['member_since'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Section supprimée : Informations financières -->
                <!-- Les tarifs ont été supprimés pour des raisons de confidentialité -->

                <!-- Contact -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact</h3>
                    @auth
                        @if(auth()->user()->role === 'client')
                            <a href="{{ route('client.messaging.create', $prestataire->id) }}" 
                               class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-envelope mr-2"></i>
                                Contacter
                            </a>
                        @endif
                    @else
                        <p class="text-gray-600 text-sm mb-4">Connectez-vous pour contacter ce prestataire</p>
                        <a href="{{ route('login') }}" 
                           class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center justify-center">
                            Se connecter
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
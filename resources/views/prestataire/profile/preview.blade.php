@extends('layouts.app')

@section('title', 'Aperçu de votre profil public')

@push('styles')
    <link href="{{ asset('css/prestataire-profile.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('prestataire.profile.edit') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour à l'édition
                </a>
                <div class="h-6 border-l border-gray-300"></div>
                <h1 class="text-xl font-semibold text-gray-800">Aperçu de votre profil public</h1>
            </div>
            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                <i class="fas fa-eye mr-1"></i>
                Mode aperçu
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Bannière -->
        <div class="relative bg-gradient-to-r from-purple-600 to-indigo-700 rounded-t-lg shadow-lg p-8 text-center h-48 flex items-center justify-center">
            <h1 class="text-4xl font-bold text-white tracking-tight">{{ $prestataire->user->name }}</h1>
        </div>

        <div class="bg-white rounded-b-lg shadow-lg px-8 pb-8">
            <!-- Photo de profil -->
            <div class="flex justify-center -mt-16">
                <div class="profile-photo" @if($prestataire && $prestataire->photo) style="background-image: url('{{ Storage::url($prestataire->photo) }}')" @endif>
                    @if(!($prestataire && $prestataire->photo))
                        <i class="fas fa-user fa-5x profile-photo-icon"></i>
                    @endif
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne principale (gauche) -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- À propos -->
                    @if($prestataire && $prestataire->description)
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                                <i class="fas fa-user-circle mr-3 text-indigo-500"></i>À propos
                            </h2>
                            <div class="text-gray-600 leading-relaxed space-y-4 text-justify">
                                {{ $prestataire->description }}
                            </div>
                        </div>
                    @endif

                    <!-- Réalisations (Portfolio) -->
                    @if($prestataire && is_array($prestataire->portfolio_images) && count($prestataire->portfolio_images) > 0)
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                                <i class="fas fa-images mr-3 text-indigo-500"></i>Réalisations
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($prestataire->portfolio_images as $item)
                                    <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                                        <a href="{{ $item['link'] ?? '#' }}" target="_blank">
                                            @if(isset($item['image']))
                                                <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['title'] ?? 'Portfolio item' }}" class="w-full h-40 object-cover">
                                            @endif
                                            <div class="p-4">
                                                <h3 class="font-semibold text-gray-800 truncate">{{ $item['title'] ?? 'Sans titre' }}</h3>
                                                @if(isset($item['link']) && $item['link'])
                                                    <span class="text-sm text-indigo-600 hover:underline">Voir le projet</span>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Avis Clients -->
                    @if(isset($reviews) && $reviews->count() > 0)
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                                <i class="fas fa-comments mr-3 text-indigo-500"></i>Avis clients
                            </h2>
                            <div class="space-y-6">
                                @foreach($reviews as $review)
                                    @if($review->client && $review->client->user)
                                        <div class="flex items-start space-x-4">
                                            @if($review->client && $review->client->photo)
                                                <div class="h-12 w-12 rounded-full bg-cover bg-center flex-shrink-0" style="background-image: url('{{ Storage::url($review->client->photo) }}')"></div>
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0">
                                                    <i class="fas fa-user text-xl"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="font-bold text-gray-800">{{ $review->client->user->name }}</h4>
                                                    <div class="text-yellow-500 flex items-center">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <p class="text-gray-600 mt-2">{{ $review->comment }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Colonne latérale (droite) -->
                <div class="space-y-6">
                    <!-- Contact -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-address-book mr-3 text-indigo-500"></i>Contact
                        </h3>
                        <div class="space-y-5">
                            <div class="flex items-start text-gray-700">
                                <i class="fas fa-envelope w-6 text-gray-400 mr-4 pt-1"></i>
                                <div>
                                    <span class="font-semibold text-gray-600">Email</span>
                                    <a href="mailto:{{ $prestataire->user->email }}" class="block text-indigo-600 hover:underline break-all">{{ $prestataire->user->email }}</a>
                                </div>
                            </div>
                            @if($prestataire && $prestataire->phone)
                                <div class="flex items-start text-gray-700">
                                    <i class="fas fa-phone w-6 text-gray-400 mr-4 pt-1"></i>
                                    <div>
                                        <span class="font-semibold text-gray-600">Téléphone</span>
                                        <a href="tel:{{ $prestataire->phone }}" class="block text-indigo-600 hover:underline">{{ $prestataire->phone }}</a>
                                    </div>
                                </div>
                            @endif
                            @if($prestataire && $prestataire->website)
                                <div class="flex items-start text-gray-700">
                                    <i class="fas fa-globe w-6 text-gray-400 mr-4 pt-1"></i>
                                    <div>
                                        <span class="font-semibold text-gray-600">Site Web</span>
                                        <a href="{{ $prestataire->website }}" target="_blank" rel="noopener noreferrer" class="block text-indigo-600 hover:underline">{{ $prestataire->website }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button class="w-full mt-8 bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-md flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i>Contacter
                        </button>
                    </div>

                    <!-- Statistiques -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-chart-pie mr-3 text-indigo-500"></i>Statistiques
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-gray-700">
                                <span class="flex items-center"><i class="fas fa-briefcase w-5 mr-3 text-gray-400"></i>Services</span>
                                <span class="font-bold text-gray-900">{{ $stats['total_services'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-700">
                                <span class="flex items-center"><i class="fas fa-check-circle w-5 mr-3 text-gray-400"></i>Projets terminés</span>
                                <span class="font-bold text-gray-900">{{ $stats['completed_bookings'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-700">
                                <span class="flex items-center"><i class="fas fa-star w-5 mr-3 text-gray-400"></i>Note moyenne</span>
                                <div class="flex items-center">
                                    <span class="font-bold text-gray-900 mr-2">{{ number_format($stats['average_rating'] ?? 0, 1) }}</span>
                                    <div class="text-yellow-400 flex items-center">
                                        @php $rating = $stats['average_rating'] ?? 0; @endphp
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($rating >= $i)
                                                <i class="fas fa-star"></i>
                                            @elseif ($rating >= $i - 0.5)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-gray-700">
                                <span class="flex items-center"><i class="fas fa-comments w-5 mr-3 text-gray-400"></i>Avis</span>
                                <span class="font-bold text-gray-900">{{ $stats['total_reviews'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                        
                        <!-- Avis récents -->
                        @if(isset($recent_reviews) && $recent_reviews->count() > 0)
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-star mr-2 text-indigo-600"></i>
                                    Avis récents
                                </h3>
                                <div class="space-y-4">
                                    @foreach($recent_reviews->take(3) as $review)
                                        <div class="border-l-4 border-indigo-200 pl-4">
                                            <div class="flex items-center mb-2">
                                                <div class="flex text-yellow-400 mr-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <i class="fas fa-star text-xs"></i>
                                                        @else
                                                            <i class="far fa-star text-xs"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{ $review->client_name }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ Str::limit($review->comment, 100) }}</p>
                                            <span class="text-xs text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
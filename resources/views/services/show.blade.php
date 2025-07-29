@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('title', $service->title . ' - Service - TaPrestation')

@section('content')
<div class="min-h-screen bg-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Accueil
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('services.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Services</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ Str::limit($service->title, 50) }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Galerie d'images -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    @if($service->images && count($service->images) > 0)
                        <div class="relative">
                            <!-- Image principale -->
                            <div class="aspect-w-16 aspect-h-12">
                                <img id="mainImage" src="{{ Storage::url($service->images[0]->image_path) }}" alt="{{ $service->title }}" class="w-full h-96 object-cover">
                            </div>
                        </div>
                        
                        <!-- Miniatures -->
                        @if(count($service->images) > 1)
                            <div class="p-4 border-t">
                                <div class="flex space-x-2 overflow-x-auto">
                                    @foreach($service->images as $index => $image)
                                        <button onclick="changeMainImage('{{ Storage::url($image->image_path) }}')"
                                                class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 {{ $index === 0 ? 'border-blue-500' : 'border-gray-200' }} hover:border-blue-500 transition duration-200">
                                            <img src="{{ Storage::url($image->image_path) }}" alt="Photo {{ $index + 1 }}" class="w-full h-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="h-96 bg-gray-200 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-500">Aucune photo disponible</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Description détaillée -->
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
                    <div class="prose max-w-none text-gray-700">
                        {!! nl2br(e($service->description)) !!}
                    </div>
                </div>
            </div>
            
            <!-- Sidebar d'informations -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <!-- Prix et titre -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-blue-900 mb-2">{{ $service->title }}</h1>
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-3xl font-bold text-blue-600">{{ number_format($service->price, 2) }}€ <span class="text-lg font-normal text-gray-500">/ {{ $service->price_type }}</span></div>
                        </div>
                    </div>
                    
                    <!-- Informations du vendeur -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-3">Prestataire</h3>
                        <div class="flex items-center mb-4">
                            <div class="relative w-12 h-12 mr-3">
                                @if($service->prestataire->photo)
                                    <img src="{{ Storage::url($service->prestataire->photo) }}" alt="{{ $service->prestataire->user->name }}" class="w-12 h-12 rounded-full object-cover">
                                @elseif($service->prestataire->user->avatar)
                                    <img src="{{ Storage::url($service->prestataire->user->avatar) }}" alt="{{ $service->prestataire->user->name }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                                @if($service->prestataire->isVerified())
                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center border-2 border-white">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-900">{{ $service->prestataire->user->name }}</span>
                                    @if($service->prestataire->isVerified())
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Vérifié
                                        </span>
                                    @endif
                                </div>
                                @if($service->prestataire->company_name)
                                    <div class="text-sm text-gray-600">{{ $service->prestataire->company_name }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-600">
                            @if($service->city)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $service->city }}
                                </div>
                            @endif
                            
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Publié {{ $service->created_at->diffForHumans() }}
                            </div>
                        </div>

                        @if ($service->latitude && $service->longitude)
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-blue-800 mb-3">Localisation sur carte</h3>
                                <div id="map" style="height: 250px;" class="rounded-lg z-10"></div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Détails du produit -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-3">Détails</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Catégories:</span>
                                <div class="font-medium text-right">
                                    @foreach($service->categories as $category)
                                        <a href="{{ route('services.index', ['category' => $category->id]) }}" class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full hover:bg-blue-200 transition-colors duration-200">{{ $category->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Référence:</span>
                                <span class="font-medium text-gray-800">#{{ $service->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Temps de livraison:</span>
                                <span class="font-medium text-gray-800">{{ $service->delivery_time }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $service->status === 'active' ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Réservable:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->reservable ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $service->reservable ? 'Oui' : 'Non' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Vues:</span>
                                <span class="font-medium text-gray-800">{{ $service->views }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-6">
                        @auth
                            @if(auth()->user()->role === 'client' && auth()->user()->id !== $service->prestataire->user_id)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <a href="{{ route('bookings.create', $service) }}" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold text-center flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 6a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 9a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1zm16 3a1 1 0 10-2 0v4a1 1 0 001 1h1a1 1 0 100-2h-1v-3zM4 12a1 1 0 10-2 0v5a1 1 0 001 1h1a1 1 0 100-2h-1v-4zM8 12a1 1 0 10-2 0v5a1 1 0 001 1h1a1 1 0 100-2h-1v-4zm4 0a1 1 0 10-2 0v5a1 1 0 001 1h1a1 1 0 100-2h-1v-4z"></path></svg>
                                        Réserver
                                    </a>
                                    <a href="{{ route('client.messaging.show', ['user' => $service->prestataire->user->id, 'service_id' => $service->id]) }}" class="w-full bg-blue-100 text-blue-800 px-4 py-3 rounded-lg hover:bg-blue-200 transition duration-200 font-semibold text-center flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                                        Contacter
                                    </a>
                                </div>
                            @elseif(auth()->user()->id === $service->prestataire->user_id)
                                <a href="{{ route('prestataire.services.edit', $service) }}" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold text-center flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                                    Modifier mon service
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold text-center flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                Se connecter pour réserver
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Services similaires -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-blue-900 mb-6">Services similaires</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($similarServices as $similarService)
                    @include('components.service-card', ['service' => $similarService])
                @empty
                    <div class="col-span-full text-center text-gray-500">
                        Aucun service similaire trouvé.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @if ($service->latitude && $service->longitude)
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            var map = L.map('map').setView([{{ $service->latitude }}, {{ $service->longitude }}], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([{{ $service->latitude }}, {{ $service->longitude }}]).addTo(map)
                .bindPopup('{{ $service->title }}')
                .openPopup();
        </script>
    @endif
    <script>
        function changeMainImage(url) {
            document.getElementById('mainImage').src = url;
            // Update border on thumbnails
            const buttons = document.querySelectorAll('.flex-shrink-0');
            buttons.forEach(button => {
                const img = button.querySelector('img');
                if (img.src === url) {
                    button.classList.add('border-blue-500');
                    button.classList.remove('border-gray-200');
                } else {
                    button.classList.remove('border-blue-500');
                    button.classList.add('border-gray-200');
                }
            });
        }
    </script>
@endpush
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('title', $urgentSale->title . ' - Vente urgente - TaPrestation')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
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
                        <a href="{{ route('urgent-sales.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-red-600 md:ml-2">Ventes urgentes</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ Str::limit($urgentSale->title, 50) }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Messages de succès/erreur -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Succès!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                        <title>Fermer</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                        <title>Fermer</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Galerie d'images -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    @if($urgentSale->photos && count($urgentSale->photos) > 0)
                        <div class="relative">
                            <!-- Image principale -->
                            <div class="aspect-w-16 aspect-h-12">
                                <img id="mainImage" src="{{ Storage::url($urgentSale->photos[0]) }}" alt="{{ $urgentSale->title }}" class="w-full h-96 object-cover">
                            </div>
                            
                            @if($urgentSale->is_urgent)
                                <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    VENTE URGENTE
                                </div>
                            @endif
                            
                            <div class="absolute top-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                                État: {{ ucfirst($urgentSale->condition) }}
                            </div>
                        </div>
                        
                        <!-- Miniatures -->
                        @if(count($urgentSale->photos) > 1)
                            <div class="p-4 border-t">
                                <div class="flex space-x-2 overflow-x-auto">
                                    @foreach($urgentSale->photos as $index => $photo)
                                        <button onclick="changeMainImage('{{ Storage::url($photo) }}')"
                                                class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 {{ $index === 0 ? 'border-red-500' : 'border-gray-200' }} hover:border-red-500 transition duration-200">
                                            <img src="{{ Storage::url($photo) }}" alt="Photo {{ $index + 1 }}" class="w-full h-full object-cover">
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
                        {!! nl2br(e($urgentSale->description)) !!}
                    </div>
                    
                    @if($urgentSale->reason)
                        <div class="mt-6 p-4 bg-red-50 rounded-lg border border-red-200">
                            <h3 class="text-sm font-medium text-red-800 mb-2">Raison de la vente urgente</h3>
                            <p class="text-sm text-red-700">{{ $urgentSale->reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Sidebar d'informations -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <!-- Prix et titre -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $urgentSale->title }}</h1>
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-3xl font-bold text-red-600">{{ number_format($urgentSale->price, 2) }}€</div>
                            @if($urgentSale->quantity > 1)
                                <div class="text-sm text-gray-600">Quantité: {{ $urgentSale->quantity }}</div>
                            @endif
                        </div>
                        
                        @if($urgentSale->is_urgent)
                            <div class="bg-red-100 border border-red-300 rounded-lg p-3 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-red-800">Vente urgente - Prix négociable</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Informations du vendeur -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Vendeur</h3>
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                @if($urgentSale->prestataire->user->avatar)
                                    <img src="{{ Storage::url($urgentSale->prestataire->user->avatar) }}" alt="{{ $urgentSale->prestataire->user->name }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <span class="text-lg font-medium text-gray-600">{{ substr($urgentSale->prestataire->user->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $urgentSale->prestataire->user->name }}</div>
                                @if($urgentSale->prestataire->company_name)
                                    <div class="text-sm text-gray-600">{{ $urgentSale->prestataire->company_name }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-600">
                            @if($urgentSale->prestataire->city)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $urgentSale->location }}
                                </div>
                            @endif
                            
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Publié {{ $urgentSale->created_at->diffForHumans() }}
                            </div>
                        </div>

                        @if ($urgentSale->latitude && $urgentSale->longitude)
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Localisation sur carte</h3>
                                <div id="map" style="height: 250px;" class="rounded-lg z-10"></div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Détails du produit -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Détails</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">État:</span>
                                <span class="font-medium">{{ ucfirst($urgentSale->condition) }}</span>
                            </div>
                            
                            @if($urgentSale->quantity > 1)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Quantité:</span>
                                    <span class="font-medium">{{ $urgentSale->quantity }}</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Référence:</span>
                                <span class="font-medium text-xs">#{{ $urgentSale->id }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="space-y-3">
                        @auth
                            @if(auth()->user()->id !== $urgentSale->prestataire->user_id)
                                <button onclick="openContactModal('{{ addslashes($urgentSale->title) }}', '{{ $urgentSale->id }}', '{{ number_format($urgentSale->price, 2) }}')" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition duration-200 font-medium">
    Contacter le vendeur
</button>
                            @else
                                <div class="bg-gray-100 text-gray-600 px-6 py-3 rounded-lg text-center">
                                    Votre annonce
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition duration-200 font-medium text-center">
                                Se connecter pour contacter
                            </a>
                        @endauth
                        
                        <button onclick="shareProduct()" class="w-full bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                            Partager
                        </button>
                        
                        <button onclick="reportProduct()" class="w-full text-red-600 hover:text-red-700 text-sm font-medium">
                            Signaler cette annonce
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ventes similaires -->
        @if($similarSales && $similarSales->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Ventes similaires</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($similarSales as $sale)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition duration-200 overflow-hidden">
                            <a href="{{ route('urgent-sales.show', $sale) }}" class="block">
                                <div class="relative h-40 bg-gray-200">
                                    @if($sale->photos && count($sale->photos) > 0)
                                        <img src="{{ Storage::url($sale->photos[0]) }}" alt="{{ $sale->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    @if($sale->is_urgent)
                                        <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-bold">
                                            URGENT
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900 mb-2 line-clamp-2">{{ Str::limit($sale->title, 40) }}</h3>
                                    <div class="text-lg font-bold text-red-600 mb-2">{{ number_format($sale->price, 2) }}€</div>
                                    <div class="text-sm text-gray-500">{{ $sale->location ?? 'Non spécifié' }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de contact -->
@auth
    @if(auth()->user()->id !== $urgentSale->prestataire->user_id)
        <div id="contactModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                    <div class="p-6">
                        <form action="{{ route('urgent-sales.contact', $urgentSale) }}" method="POST">
                            @csrf
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Contacter le vendeur</h3>
                                <button type="button" onclick="closeContactModal()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            @if ($errors->any())
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div class="mb-4">
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Votre message</label>
                                <textarea id="message" name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Votre message..." required></textarea>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="button" onclick="closeContactModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition duration-200">
                                    Annuler
                                </button>
                                <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">
                                    Envoyer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endauth

@push('scripts')
    @if ($urgentSale->latitude && $urgentSale->longitude)
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var map = L.map('map').setView([{{ $urgentSale->latitude }}, {{ $urgentSale->longitude }}], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.marker([{{ $urgentSale->latitude }}, {{ $urgentSale->longitude }}]).addTo(map)
                    .bindPopup('Localisation approximative de l\'article.')
                    .openPopup();
            });
        </script>
    @endif
@endpush

<!-- Modal de signalement -->
<div id="reportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Signaler cette annonce</h3>
                    <button onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form action="{{ route('urgent-sales.report', $urgentSale) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Raison du signalement</label>
                        <select id="reason" name="reason" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Sélectionnez une raison</option>
                            <option value="inappropriate">Contenu inapproprié</option>
                            <option value="fake">Annonce frauduleuse</option>
                            <option value="spam">Spam</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="details" class="block text-sm font-medium text-gray-700 mb-2">Détails (optionnel)</label>
                        <textarea id="details" name="details" rows="3"
                                  placeholder="Décrivez le problème..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeReportModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition duration-200">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">
                            Signaler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
    
    // Mettre à jour les bordures des miniatures
    document.querySelectorAll('.flex-shrink-0 button').forEach(btn => {
        btn.classList.remove('border-red-500');
        btn.classList.add('border-gray-200');
    });
    
    event.target.closest('button').classList.remove('border-gray-200');
    event.target.closest('button').classList.add('border-red-500');
}

function openContactModal(title, id, price) {
    const message = `Bonjour, je suis intéressé(e) par votre annonce : ${title} (#Référence : ${id}) au prix de ${price}€.`;
    document.getElementById('message').value = message;
    document.getElementById('contactModal').classList.remove('hidden');
}

function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
}

function reportProduct() {
    document.getElementById('reportModal').classList.remove('hidden');
}

function closeReportModal() {
    document.getElementById('reportModal').classList.add('hidden');
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $urgentSale->title }}',
            text: 'Découvrez cette vente urgente sur TaPrestation',
            url: window.location.href
        });
    } else {
        // Fallback pour les navigateurs qui ne supportent pas l'API Web Share
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Lien copié dans le presse-papiers!');
        });
    }
}

// Fermer les modals en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    const contactModal = document.getElementById('contactModal');
    const reportModal = document.getElementById('reportModal');
    
    if (event.target === contactModal) {
        closeContactModal();
    }
    
    if (event.target === reportModal) {
        closeReportModal();
    }
});
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.aspect-w-16 {
    position: relative;
    padding-bottom: 75%; /* 16:12 aspect ratio */
}

.aspect-w-16 > * {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
</style>
@endpush
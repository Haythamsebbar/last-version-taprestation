@extends('layouts.app')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin="" />
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center mb-8">
            <a href="{{ route('prestataire.services.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
            <i class="fas fa-concierge-bell text-blue-500 text-3xl mr-3"></i>
            <h1 class="text-4xl font-bold text-blue-700">Créer un nouveau service</h1>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-lg p-8">
            @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Veuillez corriger les erreurs suivantes :</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('prestataire.services.store') }}" enctype="multipart/form-data" id="serviceForm">
                @csrf

                <!-- Informations de base -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-blue-200">
                    <h2 class="text-2xl font-bold text-blue-700 mb-6">Informations principales</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Titre -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titre du service *</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description détaillée *</label>
                            <textarea id="description" name="description" required rows="6" maxlength="2000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror" placeholder="Décrivez en détail votre service, vos compétences et ce qui vous différencie...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reservable -->
                        <div class="md:col-span-2">
                            <label for="reservable" class="inline-flex items-center">
                                <input id="reservable" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200 focus:ring-opacity-50" name="reservable" {{ old('reservable') ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">Activer la réservation directe pour ce service</span>
                            </label>
                        </div>

                        <!-- Delivery time -->
                        <div>
                            <label for="delivery_time" class="block text-sm font-medium text-gray-700 mb-2">Délai de livraison (en jours)</label>
                            <input type="number" id="delivery_time" name="delivery_time" value="{{ old('delivery_time') }}" min="1" max="365" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('delivery_time') border-red-500 @enderror">
                            @error('delivery_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Prix -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-blue-200">
                    <h2 class="text-2xl font-bold text-blue-700 mb-6">Prix du service</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Prix (€)</label>
                            <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="price_type" class="block text-sm font-medium text-gray-700 mb-2">Type de tarification</label>
                            <select id="price_type" name="price_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price_type') border-red-500 @enderror">
                                <option value="">Sélectionnez un type</option>
                                <option value="fixe" {{ old('price_type') == 'fixe' ? 'selected' : '' }}>Prix fixe</option>
                                <option value="heure" {{ old('price_type') == 'heure' ? 'selected' : '' }}>Par heure</option>
                                <option value="jour" {{ old('price_type') == 'jour' ? 'selected' : '' }}>Par jour</option>
                                <option value="projet" {{ old('price_type') == 'projet' ? 'selected' : '' }}>Par projet</option>
                                <option value="devis" {{ old('price_type') == 'devis' ? 'selected' : '' }}>Sur devis</option>
                            </select>
                            @error('price_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Catégorie -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-blue-200">
                    <h2 class="text-2xl font-bold text-blue-700 mb-6">Catégorie du service</h2>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
                    <select id="category_id" name="category_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photos -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-blue-200">
                    <h2 class="text-2xl font-bold text-blue-700 mb-6">Photos</h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                        <div id="upload-area" class="cursor-pointer" onclick="document.getElementById('images').click()">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-600 mb-2">Cliquez pour ajouter des photos ou glissez-déposez</p>
                            <p class="text-gray-500 text-sm">Maximum 5 photos, 5MB par photo</p>
                        </div>
                        <div id="image-preview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-4 hidden"></div>
                    </div>
                    @error('images')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Localisation -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-blue-200">
                    <h2 class="text-2xl font-bold text-blue-700 mb-6">Localisation</h2>
                    <div class="map-container">
                        <div id="serviceMap" class="h-64 rounded-lg border border-gray-300"></div>
                        <div class="mt-3">
                            <input type="text" id="selectedAddress" name="address" value="{{ old('address') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror" placeholder="Cliquez sur la carte pour sélectionner une localisation" readonly>
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <div class="flex gap-3 mt-3">
                                <button type="button" id="getCurrentLocationBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200"><i class="fas fa-location-arrow mr-2"></i>Ma position actuelle</button>
                                <button type="button" id="clearLocationBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200"><i class="fas fa-times mr-2"></i>Effacer la localisation</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-8 border-t border-blue-200">
                    <a href="{{ route('prestataire.services.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md transition duration-200">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition duration-200 font-semibold">
                        <i class="fas fa-check mr-2"></i>Créer le service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Map Initialization
    let map = null;
    let marker = null;
    const defaultLat = 33.5731; // Casablanca
    const defaultLng = -7.5898;

    function initializeMap() {
        const mapElement = document.getElementById('serviceMap');
        if (!mapElement) return;

        map = L.map('serviceMap').setView([defaultLat, defaultLng], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            updateMarker(lat, lng);
            reverseGeocode(lat, lng);
        });
    }

    function updateMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
    }

    async function reverseGeocode(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=fr`);
            const data = await response.json();
            document.getElementById('selectedAddress').value = data.display_name || `Coordonnées: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        } catch (error) {
            console.error('Error during reverse geocoding:', error);
            document.getElementById('selectedAddress').value = `Coordonnées: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
    }

    document.getElementById('getCurrentLocationBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.setView([lat, lng], 13);
                updateMarker(lat, lng);
                reverseGeocode(lat, lng);
            }, function(error) {
                alert('Erreur de géolocalisation: ' + error.message);
            });
        } else {
            alert('La géolocalisation n\'est pas supportée par votre navigateur.');
        }
    });

    document.getElementById('clearLocationBtn').addEventListener('click', function() {
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        document.getElementById('selectedAddress').value = '';
        map.setView([defaultLat, defaultLng], 6);
    });

    initializeMap();

    // Image Preview
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview');
    const uploadArea = document.getElementById('upload-area');

    window.previewImages = function(input) {
        previewContainer.innerHTML = '';
        if (input.files && input.files.length > 0) {
            previewContainer.classList.remove('hidden');
            uploadArea.classList.add('hidden');
            
            const files = Array.from(input.files).slice(0, 5);
            
            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                            <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });

            if (files.length < 5) {
                const addMore = document.createElement('div');
                addMore.className = 'flex items-center justify-center h-24 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-gray-400 transition-colors';
                addMore.innerHTML = '<i class="fas fa-plus text-gray-400 text-xl"></i>';
                addMore.onclick = () => imageInput.click();
                previewContainer.appendChild(addMore);
            }
        } else {
            previewContainer.classList.add('hidden');
            uploadArea.classList.remove('hidden');
        }
    }

    window.removeImage = function(index) {
        const dt = new DataTransfer();
        const files = imageInput.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) {
                dt.items.add(files[i]);
            }
        }
        imageInput.files = dt.files;
        previewImages(imageInput);
    }
});
</script>
@endpush

@push('styles')
<style>
.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}
</style>
@endpush
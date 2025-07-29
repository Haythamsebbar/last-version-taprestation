@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush

@section('title', 'Ajouter une Vente Urgente')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center mb-8">
    <a href="{{ route('prestataire.urgent-sales.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
        <i class="fas fa-arrow-left text-2xl"></i>
    </a>
    <i class="fas fa-bolt text-red-500 text-3xl mr-3"></i>
    <h1 class="text-4xl font-bold text-red-700">Ajouter une Vente Urgente</h1>
</div>

        <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-xl shadow-lg p-8">
            <form action="{{ route('prestataire.urgent-sales.store') }}" method="POST" enctype="multipart/form-data" id="urgent-sale-form">
                @csrf
                
                <!-- Informations de base -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-red-200">
    <h2 class="text-2xl font-bold text-red-700 mb-6">Informations de base</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Titre -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titre de la vente *</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Prix -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Prix (€) *</label>
                            <input type="number" id="price" name="price" value="{{ old('price') }}" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- État -->
                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">État *</label>
                            <select id="condition" name="condition" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('condition') border-red-500 @enderror">
                                <option value="">Sélectionner l'état</option>
                                <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>Neuf</option>
                                <option value="like_new" {{ old('condition') === 'like_new' ? 'selected' : '' }}>Comme neuf</option>
                                <option value="good" {{ old('condition') === 'good' ? 'selected' : '' }}>Bon état</option>
                                <option value="fair" {{ old('condition') === 'fair' ? 'selected' : '' }}>État correct</option>
                                <option value="poor" {{ old('condition') === 'poor' ? 'selected' : '' }}>Mauvais état</option>
                            </select>
                            @error('condition')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Quantité -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantité *</label>
                            <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror">
                            @error('quantity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Localisation -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Localisation *</label>
                            <div class="map-container">
                                <div id="urgentSaleMap" class="h-64 rounded-lg border border-gray-300"></div>
                                <div class="mt-3">
                                    <input type="text" id="selectedAddress" name="location" value="{{ old('location') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 @error('location') border-red-500 @enderror" placeholder="Cliquez sur la carte pour sélectionner une localisation" readonly>
                                    <input type="hidden" id="selectedLatitude" name="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" id="selectedLongitude" name="longitude" value="{{ old('longitude') }}">
                                    @error('location')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    @error('latitude')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    @error('longitude')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <div class="flex gap-3 mt-3">
                                        <button type="button" id="getCurrentLocationBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition duration-200"><i class="fas fa-location-arrow mr-2"></i>Ma position actuelle</button>
                                        <button type="button" id="clearLocationBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200"><i class="fas fa-times mr-2"></i>Effacer la localisation</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-red-200">
    <h2 class="text-2xl font-bold text-red-700 mb-6">Description</h2>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description détaillée *</label>
                    <textarea id="description" name="description" required rows="6" maxlength="2000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror" placeholder="Décrivez votre produit en détail : caractéristiques, raison de la vente, défauts éventuels...">{{ old('description') }}</textarea>
                    <div class="flex justify-between mt-1">
                        @error('description')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @else
                            <p class="text-gray-500 text-sm">Décrivez votre produit de manière détaillée</p>
                        @enderror
                        <p class="text-gray-500 text-sm"><span id="description-count">0</span>/2000</p>
                    </div>
                </div>
                
                <!-- Photos -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-red-200">
    <h2 class="text-2xl font-bold text-red-700 mb-6">Photos</h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                        <div id="upload-area" class="cursor-pointer" onclick="document.getElementById('photos').click()">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-600 mb-2">Cliquez pour ajouter des photos ou glissez-déposez</p>
                            <p class="text-gray-500 text-sm">Maximum 5 photos, 5MB par photo</p>
                        </div>
                        
                        <!-- Prévisualisation des images -->
                        <div id="image-preview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-4 hidden"></div>
                    </div>
                    @error('photos')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('photos.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Options -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-red-200">
    <h2 class="text-2xl font-bold text-red-700 mb-6">Options</h2>
                    <div class="flex items-center">
                        <input type="checkbox" id="is_urgent" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_urgent" class="ml-2 block text-sm text-gray-900">
                            <span class="font-medium">Vente urgente</span>
                            <span class="text-gray-600">- Mettre en avant cette vente (badge rouge "URGENT")</span>
                        </label>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex justify-between items-center pt-8 border-t border-red-200">
                    <a href="{{ route('prestataire.urgent-sales.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md transition duration-200">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                    
                    <div class="flex gap-3">
                        <button type="submit" name="status" value="inactive" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-md transition duration-200">
                            <i class="fas fa-save mr-2"></i>Enregistrer en brouillon
                        </button>
                        
                        <button type="submit" name="status" value="active" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg transition duration-200 font-semibold">
                            <i class="fas fa-check mr-2"></i>Publier maintenant
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Initialisation de la carte Leaflet pour urgent sale
let urgentSaleMap = null;
let currentMarker = null;

function initializeUrgentSaleMap() {
    const mapElement = document.getElementById('urgentSaleMap');
    if (!mapElement) return;

    const defaultLat = 33.5731;
    const defaultLng = -7.5898;

    urgentSaleMap = L.map('urgentSaleMap', {
        center: [defaultLat, defaultLng],
        zoom: 6
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(urgentSaleMap);

    urgentSaleMap.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (currentMarker) urgentSaleMap.removeLayer(currentMarker);

        currentMarker = L.marker([lat, lng]).addTo(urgentSaleMap);

        document.getElementById('selectedLatitude').value = lat.toFixed(6);
        document.getElementById('selectedLongitude').value = lng.toFixed(6);

        reverseGeocode(lat, lng);
    });

    setTimeout(() => urgentSaleMap.invalidateSize(), 250);
}

async function reverseGeocode(lat, lng) {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=fr`);
        const data = await response.json();
        document.getElementById('selectedAddress').value = data.display_name || `Coordonnées: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    } catch (error) {
        document.getElementById('selectedAddress').value = `Coordonnées: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
}

function clearLocation() {
    if (currentMarker) urgentSaleMap.removeLayer(currentMarker);
    currentMarker = null;
    document.getElementById('selectedLatitude').value = '';
    document.getElementById('selectedLongitude').value = '';
    document.getElementById('selectedAddress').value = '';
}

function getCurrentLocation() {
    if (!navigator.geolocation) {
        alert('La géolocalisation n\'est pas supportée.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        position => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            urgentSaleMap.setView([lat, lng], 15);
            if (currentMarker) urgentSaleMap.removeLayer(currentMarker);
            currentMarker = L.marker([lat, lng]).addTo(urgentSaleMap);

            document.getElementById('selectedLatitude').value = lat.toFixed(6);
            document.getElementById('selectedLongitude').value = lng.toFixed(6);

            reverseGeocode(lat, lng);
        },
        error => alert('Erreur de géolocalisation: ' + error.message)
    );
}

document.addEventListener('DOMContentLoaded', () => {
    initializeUrgentSaleMap();
    document.getElementById('getCurrentLocationBtn').addEventListener('click', getCurrentLocation);
    document.getElementById('clearLocationBtn').addEventListener('click', clearLocation);
});
// Compteur de caractères pour la description
document.getElementById('description').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('description-count').textContent = count;
    
    if (count > 2000) {
        this.classList.add('border-red-500');
    } else {
        this.classList.remove('border-red-500');
    }
});

// Prévisualisation des images
function previewImages(input) {
    const preview = document.getElementById('image-preview');
    const uploadArea = document.getElementById('upload-area');
    
    preview.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        preview.classList.remove('hidden');
        uploadArea.classList.add('hidden');
        
        // Limiter à 5 images
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
                    preview.appendChild(div);
                };
                
                reader.readAsDataURL(file);
            }
        });
        
        // Ajouter un bouton pour ajouter plus d'images si moins de 5
        if (files.length < 5) {
            const addMore = document.createElement('div');
            addMore.className = 'flex items-center justify-center h-24 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-gray-400 transition-colors';
            addMore.innerHTML = '<i class="fas fa-plus text-gray-400 text-xl"></i>';
            addMore.onclick = () => document.getElementById('photos').click();
            preview.appendChild(addMore);
        }
    } else {
        preview.classList.add('hidden');
        uploadArea.classList.remove('hidden');
    }
}

function removeImage(index) {
    const input = document.getElementById('photos');
    const dt = new DataTransfer();
    
    for (let i = 0; i < input.files.length; i++) {
        if (i !== index) {
            dt.items.add(input.files[i]);
        }
    }
    
    input.files = dt.files;
    previewImages(input);
}

// Validation du formulaire
document.getElementById('urgent-sale-form').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    const price = document.getElementById('price').value;
    const condition = document.getElementById('condition').value;
    const quantity = document.getElementById('quantity').value;
    const location = document.getElementById('location').value.trim();
    const description = document.getElementById('description').value.trim();
    
    if (!title || !price || !condition || !quantity || !location || !description) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires.');
        return;
    }
    
    if (parseFloat(price) <= 0) {
        e.preventDefault();
        alert('Le prix doit être supérieur à 0.');
        return;
    }
    
    if (parseInt(quantity) <= 0) {
        e.preventDefault();
        alert('La quantité doit être supérieure à 0.');
        return;
    }
    
    if (description.length > 2000) {
        e.preventDefault();
        alert('La description ne peut pas dépasser 2000 caractères.');
        return;
    }
});

// Initialiser le compteur de caractères
document.addEventListener('DOMContentLoaded', function() {
    const description = document.getElementById('description');
    document.getElementById('description-count').textContent = description.value.length;
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
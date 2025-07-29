@extends('layouts.app')

@section('title', 'Ajouter un équipement')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center mb-8">
            <a href="{{ route('prestataire.equipment.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
            <i class="fas fa-tools text-green-500 text-3xl mr-3"></i>
            <h1 class="text-4xl font-bold text-green-700">Ajouter un équipement</h1>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-xl shadow-lg p-8">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Oups!</strong>
                    <span class="block sm:inline">Quelque chose s'est mal passé.</span>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('prestataire.equipment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Informations de base -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-green-200">
                    <h2 class="text-2xl font-bold text-green-700 mb-6">Informations de base</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Nom de l'équipement -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'équipement *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ex: Perceuse sans fil Bosch" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catégorie -->
                        <div class="md:col-span-2">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
                            <select id="category_id" name="category_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('category_id') border-red-500 @enderror">
                                <option value="">Choisissez une catégorie</option>
                                @foreach ($categories as $category)
                                    <optgroup label="{{ $category->name }}">
                                        @if($category->children->count() > 0)
                                            @foreach ($category->children as $child)
                                                <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endif
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Localisation -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-green-200">
                    <h2 class="text-2xl font-bold text-green-700 mb-6">Localisation *</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Ex: 123 Rue de Paris">
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Ville *</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('city') border-red-500 @enderror" placeholder="Ex: Paris">
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Pays *</label>
                            <input type="text" name="country" id="country" value="{{ old('country') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('country') border-red-500 @enderror" placeholder="Ex: France">
                            @error('country')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6">
                            <div id="map" class="h-64 rounded-lg border border-gray-300"></div>
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                            @error('latitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('longitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <div class="flex gap-3 mt-3">
                                <button type="button" id="getCurrentLocationBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200"><i class="fas fa-location-arrow mr-2"></i>Ma position actuelle</button>
                                <button type="button" id="clearLocationBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200"><i class="fas fa-times mr-2"></i>Effacer la localisation</button>
                            </div>
                        </div>
                </div>

                <!-- Description et Spécifications -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-green-200">
                    <h2 class="text-2xl font-bold text-green-700 mb-6">Détails de l'équipement</h2>
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description courte *</label>
                        <textarea id="description" name="description" rows="4" required placeholder="Décrivez brièvement votre équipement, son état et ses caractéristiques principales" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Spécifications techniques -->
                    <div class="mt-6">
                        <label for="technical_specifications" class="block text-sm font-medium text-gray-700 mb-2">Spécifications techniques</label>
                        <textarea id="technical_specifications" name="technical_specifications" rows="4" placeholder="Dimensions, poids, puissance, capacité..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('technical_specifications') }}</textarea>
                    </div>
                </div>

                <!-- Photos -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-green-200">
                    <h2 class="text-2xl font-bold text-green-700 mb-6">Photo principale *</h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input type="file" name="main_photo" id="main_photo" required accept="image/*" class="hidden" onchange="previewMainImage(this)">
                        <div id="upload-area" class="cursor-pointer" onclick="document.getElementById('main_photo').click()">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-600 mb-2">Cliquez pour ajouter une photo ou glissez-déposez</p>
                            <p class="text-gray-500 text-sm">Ajoutez une photo claire de votre équipement</p>
                        </div>
                        <div id="image-preview" class="mt-4 hidden"></div>
                    </div>
                    @error('main_photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tarifs -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-green-200">
                    <h2 class="text-2xl font-bold text-green-700 mb-6">Tarifs et Caution</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label for="price_per_day" class="block text-sm font-medium text-gray-700 mb-2">Prix par jour (€) *</label>
                            <input type="number" name="price_per_day" id="price_per_day" value="{{ old('price_per_day') }}" required min="0" step="0.01" placeholder="50" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('price_per_day') border-red-500 @enderror">
                            @error('price_per_day')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="security_deposit" class="block text-sm font-medium text-gray-700 mb-2">Caution (€) *</label>
                            <input type="number" name="security_deposit" id="security_deposit" value="{{ old('security_deposit') }}" required min="0" step="0.01" placeholder="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('security_deposit') border-red-500 @enderror">
                            @error('security_deposit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="price_per_hour" class="block text-sm font-medium text-gray-700 mb-2">Prix par heure (€)</label>
                            <input type="number" name="price_per_hour" id="price_per_hour" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="10.00" value="{{ old('price_per_hour') }}">
                        </div>
                        <div>
                            <label for="price_per_week" class="block text-sm font-medium text-gray-700 mb-2">Prix par semaine (€)</label>
                            <input type="number" name="price_per_week" id="price_per_week" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="300.00" value="{{ old('price_per_week') }}">
                        </div>
                        <div>
                            <label for="price_per_month" class="block text-sm font-medium text-gray-700 mb-2">Prix par mois (€)</label>
                            <input type="number" name="price_per_month" id="price_per_month" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="1000.00" value="{{ old('price_per_month') }}">
                        </div>
                    </div>
                </div>

                <!-- Options et Conditions -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-green-200">
                    <h2 class="text-2xl font-bold text-green-700 mb-6">Options et Conditions</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <input id="delivery_included" name="delivery_included" type="checkbox" value="1" {{ old('delivery_included') ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="delivery_included" class="ml-3 block text-sm font-medium text-gray-700">Livraison incluse dans le prix</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="license_required" id="license_required" value="1" {{ old('license_required') ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="license_required" class="ml-3 block text-sm font-medium text-gray-700">Permis ou certification requis</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="is_available" class="ml-3 block text-sm font-medium text-gray-700">Équipement disponible immédiatement</label>
                        </div>

                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700">État de l'équipement</label>
                            <select name="condition" id="condition" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Sélectionner l'état</option>
                                <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="very_good" {{ old('condition') == 'very_good' ? 'selected' : '' }}>Très bon</option>
                                <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Bon</option>
                                <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Correct</option>
                                <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Mauvais</option>
                            </select>
                        </div>

                        <div>
                            <label for="rental_conditions" class="block text-sm font-medium text-gray-700">Conditions de location</label>
                            <textarea name="rental_conditions" id="rental_conditions" rows="3" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Conditions particulières, restrictions d'usage...">{{ old('rental_conditions') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Période de disponibilité -->
                <div class="mb-10 bg-white rounded-lg shadow-md p-6 border border-green-200">
                    <h2 class="text-2xl font-bold text-green-700 mb-6">Période de disponibilité</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="available_from" class="block text-sm font-medium text-gray-700 mb-2">Disponible à partir du</label>
                            <input type="date" name="available_from" id="available_from" value="{{ old('available_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('available_from') border-red-500 @enderror">
                            @error('available_from')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Laissez vide si disponible immédiatement</p>
                        </div>
                        
                        <div>
                            <label for="available_until" class="block text-sm font-medium text-gray-700 mb-2">Disponible jusqu'au</label>
                            <input type="date" name="available_until" id="available_until" value="{{ old('available_until') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('available_until') border-red-500 @enderror">
                            @error('available_until')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Laissez vide si pas de limite de temps</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Information sur les dates de disponibilité :</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Ces dates définissent la période générale où votre équipement peut être loué</li>
                                    <li>Vous pourrez toujours bloquer des dates spécifiques plus tard</li>
                                    <li>Les clients ne pourront réserver que dans cette période</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-8 border-t border-green-200">
                    <a href="{{ route('prestataire.equipment.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md transition duration-200">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                    
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg transition duration-200 font-semibold">
                        <i class="fas fa-check mr-2"></i>Publier l'équipement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
let map, marker;

function initMap() {
    const lat = parseFloat(document.getElementById('latitude').value) || 48.8566;
    const lon = parseFloat(document.getElementById('longitude').value) || 2.3522;

    map = L.map('map').setView([lat, lon], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    marker = L.marker([lat, lon], { draggable: true }).addTo(map);

    if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
        fetchAddress(lat, lon);
    }

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateLatLng(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', function(e) {
        const latlng = marker.getLatLng();
        updateLatLng(latlng.lat, latlng.lng);
    });
}

function updateLatLng(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    fetchAddress(lat, lng);
}

function fetchAddress(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
            if (data.address) {
                document.getElementById('address').value = data.address.road || '';
                document.getElementById('city').value = data.address.city || data.address.town || data.address.village || '';
                document.getElementById('country').value = data.address.country || '';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération de l\'adresse:', error);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    initMap();

    document.getElementById('getCurrentLocationBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const newLatLng = new L.LatLng(lat, lng);
                marker.setLatLng(newLatLng);
                map.setView(newLatLng, 13);
                updateLatLng(lat, lng);
            }, function() {
                alert('Impossible de récupérer votre position.');
            });
        } else {
            alert('La géolocalisation n\'est pas supportée par votre navigateur.');
        }
    });

    document.getElementById('clearLocationBtn').addEventListener('click', function() {
        const defaultLat = 48.8566;
        const defaultLon = 2.3522;
        const defaultLatLng = new L.LatLng(defaultLat, defaultLon);
        
        marker.setLatLng(defaultLatLng);
        map.setView(defaultLatLng, 5);
        
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        document.getElementById('address').value = '';
        document.getElementById('city').value = '';
        document.getElementById('country').value = '';
    });
});

function previewMainImage(input) {
    const preview = document.getElementById('image-preview');
    const uploadArea = document.getElementById('upload-area');
    
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        preview.classList.remove('hidden');
        uploadArea.classList.add('hidden');
        
        const file = input.files[0];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group w-32 h-32';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">
                    <button type="button" onclick="removeMainImage()" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                preview.appendChild(div);
            };
            
            reader.readAsDataURL(file);
        }
    } else {
        preview.classList.add('hidden');
        uploadArea.classList.remove('hidden');
    }
}

function removeMainImage() {
    const input = document.getElementById('main_photo');
    input.value = '';
    previewMainImage(input);
}
</script>
@endpush

@push('styles')
<style>
.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}
</style>
@endpush
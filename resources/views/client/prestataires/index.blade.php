@extends('layouts.app')

@section('content')
<div class="bg-blue-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-extrabold text-blue-900 mb-2">Trouver un prestataire</h1>
                <p class="text-lg text-blue-700">Découvrez les meilleurs prestataires pour vos besoins</p>
            </div>
    
            <!-- Filtres -->
            <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6 mb-8">
                <h2 class="text-2xl font-bold text-blue-800 mb-5 border-b-2 border-blue-200 pb-3">Filtrer les prestataires</h2>
        
        <form action="{{ route('client.browse.prestataires') }}" method="GET" class="space-y-6">
            <!-- Filtres -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Filtre par catégorie principale -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie de service</label>
                    <select name="category" id="category" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories->whereNull('parent_id') as $category)
                            <option value="{{ $category->id }}" {{ isset($filters['category']) && $filters['category'] == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre par sous-catégorie -->
                <div>
                    <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-1">Sous-catégorie</label>
                    <select name="subcategory" id="subcategory" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" disabled>
                        <option value="">Sélectionnez d'abord une catégorie</option>
                    </select>
                </div>
            </div>
            
            <!-- Filtrage par proximité géographique -->
            <div class="border-t-2 border-blue-100 pt-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">Filtrer par proximité</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Champ de localisation -->
                    <div>
                        <label for="user_location" class="block text-sm font-medium text-gray-700 mb-1">Votre localisation</label>
                        <div class="relative">
                            <input type="text" name="user_location" id="user_location" 
                                   placeholder="Ville, région ou code postal" 
                                   value="{{ request('user_location') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                            <button type="button" id="use_current_location" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-xs font-medium transition duration-200">
                                📍 Ma position
                            </button>
                        </div>
                        <input type="hidden" name="user_latitude" id="user_latitude" value="{{ request('user_latitude') }}">
                        <input type="hidden" name="user_longitude" id="user_longitude" value="{{ request('user_longitude') }}">
                    </div>
                    
                    <!-- Sélecteur de rayon -->
                    <div>
                        <label for="radius" class="block text-sm font-medium text-gray-700 mb-1">Rayon de recherche</label>
                        <select name="radius" id="radius" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                            <option value="">Tout le pays</option>
                            <option value="5" {{ request('radius') == '5' ? 'selected' : '' }}>5 km</option>
                            <option value="10" {{ request('radius') == '10' ? 'selected' : '' }}>10 km</option>
                            <option value="20" {{ request('radius') == '20' ? 'selected' : '' }}>20 km</option>
                            <option value="50" {{ request('radius') == '50' ? 'selected' : '' }}>50 km</option>
                            <option value="100" {{ request('radius') == '100' ? 'selected' : '' }}>100 km</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Filtrer
                </button>
            </div>
        </form>
    </div>
    
    <!-- Résultats -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <p class="text-gray-600">{{ $prestataires->total() }} prestataire(s) trouvé(s)</p>
            
            @if(count(array_filter($filters)) > 0)
                <a href="{{ route('client.browse.prestataires') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold px-4 py-2 rounded-lg text-center transition duration-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Réinitialiser les filtres
                </a>
            @endif
        </div>
        
        @if($prestataires->isEmpty())
            <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Aucun prestataire trouvé</h2>
                <p class="text-gray-600 mb-6">Essayez de modifier vos critères de recherche.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($prestataires as $prestataire)
                    <div class="bg-white rounded-xl shadow-lg border border-blue-200 overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                        <!-- En-tête avec nom et statut -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 border-b border-blue-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-blue-900">{{ $prestataire->user->name }}</h3>
                                <div class="flex items-center space-x-2">
                                    @if($prestataire->isVerified())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Vérifié
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Non vérifié
                                        </span>
                                    @endif
                                    @if($prestataire->reviews_avg_rating && $prestataire->reviews_avg_rating > 0)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <span class="ml-1 text-sm font-medium text-gray-700">{{ number_format($prestataire->reviews_avg_rating, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Avatar et résumé du profil -->
                            <div class="flex items-start mb-4">
                                <div class="flex-shrink-0 mr-4">
                                    @if($prestataire->photo)
                                        <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="{{ $prestataire->user->name }}" 
                                            class="w-16 h-16 rounded-full object-cover border-3 border-blue-200 shadow-md">
                                    @elseif($prestataire->user->avatar)
                                        <img src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="{{ $prestataire->user->name }}" 
                                            class="w-16 h-16 rounded-full object-cover border-3 border-blue-200 shadow-md">
                                    @elseif($prestataire->user->profile_photo_url)
                                        <img src="{{ $prestataire->user->profile_photo_url }}" alt="{{ $prestataire->user->name }}" 
                                            class="w-16 h-16 rounded-full object-cover border-3 border-blue-200 shadow-md">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-1">
                                    <p class="text-blue-800 font-semibold text-sm mb-1">{{ $prestataire->sector }}</p>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        {{ Str::limit($prestataire->description, 80) }}
                                    </p>
                                    @if($prestataire->experience_years)
                                        <p class="text-blue-600 text-xs font-medium mt-1">{{ $prestataire->experience_years }} ans d'expérience</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Données clés avec icônes -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <!-- Nombre de services -->
                                <div class="flex items-center bg-blue-50 rounded-lg p-2">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-xs text-gray-600">Services</p>
                                        <p class="text-sm font-bold text-blue-900">{{ $prestataire->services->count() }}</p>
                                    </div>
                                </div>

                                <!-- Localisation -->
                                @if($prestataire->city)
                                    <div class="flex items-center bg-green-50 rounded-lg p-2">
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-xs text-gray-600">
                                                @if(request('radius') && isset($prestataire->distance))
                                                    Distance
                                                @else
                                                    Ville
                                                @endif
                                            </p>
                                            <p class="text-sm font-bold text-green-900">
                                                @if(request('radius') && isset($prestataire->distance))
                                                    {{ round($prestataire->distance, 1) }} km
                                                @else
                                                    {{ $prestataire->city }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Note moyenne -->
                                @if($prestataire->reviews_avg_rating && $prestataire->reviews_avg_rating > 0)
                                    <div class="flex items-center bg-yellow-50 rounded-lg p-2">
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-xs text-gray-600">Note</p>
                                            <p class="text-sm font-bold text-yellow-900">{{ number_format($prestataire->reviews_avg_rating, 1) }}/5</p>
                                            <p class="text-xs text-gray-500">({{ $prestataire->reviews_count }} avis)</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center bg-gray-50 rounded-lg p-2">
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-xs text-gray-600">Note</p>
                                            <p class="text-sm font-bold text-gray-500">Aucun avis</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Membre depuis -->
                                <div class="flex items-center bg-purple-50 rounded-lg p-2">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v2m0 0V7a2 2 0 012-2h10a2 2 0 012 2v.93"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-xs text-gray-600">Membre</p>
                                        <p class="text-sm font-bold text-purple-900">{{ $prestataire->user->created_at->format('M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Compétences -->
                            @if($prestataire->skills->isNotEmpty())
                                <div class="mb-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($prestataire->skills->take(3) as $skill)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                {{ $skill->name }}
                                            </span>
                                        @endforeach
                                        
                                        @if($prestataire->skills->count() > 3)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                                +{{ $prestataire->skills->count() - 3 }} autres
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Actions -->
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('client.browse.prestataire', $prestataire->id) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Voir le profil
                                    </a>
                                    <button class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $prestataires->withQueryString()->links() }}
            </div>
        @endif
    </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const subcategorySelect = document.getElementById('subcategory');
    
    categorySelect.addEventListener('change', function() {
        const selectedCategoryId = this.value;
        
        // Réinitialiser le select des sous-catégories
        subcategorySelect.innerHTML = '<option value="">Chargement...</option>';
        subcategorySelect.disabled = true;
        
        if (selectedCategoryId) {
            // Charger les sous-catégories via AJAX
            fetch(`/api/categories/${selectedCategoryId}/subcategories`)
                .then(response => response.json())
                .then(subcategories => {
                    subcategorySelect.innerHTML = '<option value="">Sélectionnez une sous-catégorie</option>';
                    
                    if (subcategories.length > 0) {
                        subcategorySelect.disabled = false;
                        
                        subcategories.forEach(function(subcategory) {
                            const option = document.createElement('option');
                            option.value = subcategory.id;
                            option.textContent = subcategory.name;
                            
                            // Vérifier si cette sous-catégorie était sélectionnée
                            @if(isset($filters['subcategory']))
                                if (subcategory.id == '{{ $filters['subcategory'] }}') {
                                    option.selected = true;
                                }
                            @endif
                            
                            subcategorySelect.appendChild(option);
                        });
                    } else {
                        subcategorySelect.innerHTML = '<option value="">Aucune sous-catégorie disponible</option>';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des sous-catégories:', error);
                    subcategorySelect.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        } else {
            // Désactiver le select des sous-catégories
            subcategorySelect.disabled = true;
            subcategorySelect.innerHTML = '<option value="">Sélectionnez d\'abord une catégorie</option>';
        }
    });
    
    // Déclencher l'événement change au chargement si une catégorie est déjà sélectionnée
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
    
    // Géolocalisation
    const useCurrentLocationBtn = document.getElementById('use_current_location');
    const userLocationInput = document.getElementById('user_location');
    const userLatitudeInput = document.getElementById('user_latitude');
    const userLongitudeInput = document.getElementById('user_longitude');
    
    useCurrentLocationBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            useCurrentLocationBtn.textContent = '🔄 Localisation...';
            useCurrentLocationBtn.disabled = true;
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    
                    userLatitudeInput.value = latitude;
                    userLongitudeInput.value = longitude;
                    
                    // Géocodage inverse pour obtenir l'adresse
                    fetch(`/api/reverse-geocode?lat=${latitude}&lng=${longitude}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.address) {
                                userLocationInput.value = data.address;
                            } else {
                                userLocationInput.value = `${latitude}, ${longitude}`;
                            }
                        })
                        .catch(error => {
                            console.error('Erreur géocodage inverse:', error);
                            userLocationInput.value = `${latitude}, ${longitude}`;
                        });
                     
                     console.log('Position obtenue:', latitude, longitude);
                     
                     useCurrentLocationBtn.textContent = '📍 Ma position';
                     useCurrentLocationBtn.disabled = false;
                },
                function(error) {
                    console.error('Erreur géolocalisation:', error);
                    alert('Impossible d\'obtenir votre position. Veuillez saisir votre localisation manuellement.');
                    useCurrentLocationBtn.textContent = '📍 Ma position';
                    useCurrentLocationBtn.disabled = false;
                }
            );
        } else {
            alert('La géolocalisation n\'est pas supportée par votre navigateur.');
        }
    });
    
    // Géocodage pour la saisie manuelle
    let geocodeTimeout;
    userLocationInput.addEventListener('input', function() {
        clearTimeout(geocodeTimeout);
        const location = this.value.trim();
        
        if (location.length > 3) {
            geocodeTimeout = setTimeout(() => {
                // Géocodage simple sans API externe pour éviter les clés
                // On peut utiliser une API de géocodage gratuite ou implémenter côté serveur
                fetch(`/api/geocode?address=${encodeURIComponent(location)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.latitude && data.longitude) {
                            userLatitudeInput.value = data.latitude;
                            userLongitudeInput.value = data.longitude;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur géocodage:', error);
                    });
            }, 1000);
        }
    });
});
</script>
@endpush
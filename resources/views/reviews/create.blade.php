@extends('layouts.app')

@section('title', 'Laisser un avis')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Laisser un avis</h1>
            
            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <input type="hidden" name="prestataire_id" value="{{ $prestataireId }}">
                @if($bookingId)
                    <input type="hidden" name="booking_id" value="{{ $bookingId }}">
                @endif
                
                <!-- Note globale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note globale *</label>
                    <div class="flex items-center space-x-2">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="rating" value="{{ $i }}" class="sr-only rating-input" required>
                                <svg class="w-8 h-8 text-gray-300 hover:text-yellow-400 transition-colors star-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </label>
                        @endfor
                    </div>
                    <div class="text-sm text-gray-500 mt-1" id="rating-label">Sélectionnez une note</div>
                    @error('rating')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Notation multi-critères -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Évaluation détaillée</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Ponctualité -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ponctualité</label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="punctuality_rating" value="{{ $i }}" class="sr-only criteria-rating" data-criteria="punctuality">
                                        <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors criteria-star" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Qualité du service -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Qualité du service</label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="quality_rating" value="{{ $i }}" class="sr-only criteria-rating" data-criteria="quality">
                                        <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors criteria-star" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Rapport qualité/prix -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rapport qualité/prix</label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="value_rating" value="{{ $i }}" class="sr-only criteria-rating" data-criteria="value">
                                        <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors criteria-star" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Communication -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Communication</label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="communication_rating" value="{{ $i }}" class="sr-only criteria-rating" data-criteria="communication">
                                        <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors criteria-star" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Commentaire -->
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Commentaire</label>
                    <textarea name="comment" id="comment" rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Partagez votre expérience avec ce prestataire...">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Photos -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photos (optionnel)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                        <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                        <label for="photos" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="mt-2">
                                <span class="text-blue-600 font-medium">Cliquez pour ajouter des photos</span>
                                <p class="text-gray-500 text-sm mt-1">PNG, JPG, GIF jusqu'à 2MB chacune</p>
                            </div>
                        </label>
                    </div>
                    <div id="image-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                    @error('photos.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Boutons -->
                <div class="flex justify-between pt-6">
                    <a href="{{ url()->previous() }}" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Publier l'avis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion des étoiles pour la note globale
const ratingInputs = document.querySelectorAll('.rating-input');
const starIcons = document.querySelectorAll('.star-icon');
const ratingLabel = document.getElementById('rating-label');

const ratingLabels = {
    1: '★ Très mauvais',
    2: '★★ Mauvais', 
    3: '★★★ Correct',
    4: '★★★★ Bon',
    5: '★★★★★ Excellent'
};

ratingInputs.forEach((input, index) => {
    input.addEventListener('change', function() {
        const rating = parseInt(this.value);
        ratingLabel.textContent = ratingLabels[rating];
        
        starIcons.forEach((star, starIndex) => {
            if (starIndex < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    });
});

// Gestion des étoiles pour les critères
const criteriaInputs = document.querySelectorAll('.criteria-rating');
criteriaInputs.forEach(input => {
    input.addEventListener('change', function() {
        const criteria = this.dataset.criteria;
        const rating = parseInt(this.value);
        const criteriaStars = this.closest('div').querySelectorAll('.criteria-star');
        
        criteriaStars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    });
});

// Prévisualisation des images
function previewImages(input) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                        <button type="button" onclick="removeImage(this, ${index})" 
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                            ×
                        </button>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

function removeImage(button, index) {
    const input = document.getElementById('photos');
    const dt = new DataTransfer();
    
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    button.closest('div').remove();
}
</script>
@endsection
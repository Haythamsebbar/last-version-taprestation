@extends('layouts.app')

@section('title', 'Modifier la vente - ' . $urgentSale->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="flex items-center mb-6">
            <a href="{{ route('prestataire.urgent-sales.show', $urgentSale) }}" class="text-red-600 hover:text-red-800 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-red-800">Modifier la vente</h1>
                <p class="text-gray-600 mt-1">{{ $urgentSale->title }}</p>
            </div>
        </div>
        
        <!-- Formulaire -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('prestataire.urgent-sales.update', $urgentSale) }}" method="POST" enctype="multipart/form-data" id="urgent-sale-form">
                @csrf
                @method('PUT')
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Titre -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Titre de la vente <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title', $urgentSale->title) }}" required maxlength="255" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Prix -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                Prix (€) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="price" name="price" value="{{ old('price', $urgentSale->price) }}" required min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('price') border-red-500 @enderror">
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- État -->
                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                                État <span class="text-red-500">*</span>
                            </label>
                            <select id="condition" name="condition" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('condition') border-red-500 @enderror">
                                <option value="">Sélectionner un état</option>
                                <option value="new" {{ old('condition', $urgentSale->condition) === 'new' ? 'selected' : '' }}>Neuf</option>
                                <option value="like_new" {{ old('condition', $urgentSale->condition) === 'like_new' ? 'selected' : '' }}>Comme neuf</option>
                                <option value="very_good" {{ old('condition', $urgentSale->condition) === 'very_good' ? 'selected' : '' }}>Très bon état</option>
                                <option value="good" {{ old('condition', $urgentSale->condition) === 'good' ? 'selected' : '' }}>Bon état</option>
                                <option value="fair" {{ old('condition', $urgentSale->condition) === 'fair' ? 'selected' : '' }}>État correct</option>
                                <option value="poor" {{ old('condition', $urgentSale->condition) === 'poor' ? 'selected' : '' }}>Mauvais état</option>
                            </select>
                            @error('condition')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Quantité -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Quantité <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $urgentSale->quantity) }}" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('quantity') border-red-500 @enderror">
                            @error('quantity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Localisation -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Localisation <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="location" name="location" value="{{ old('location', $urgentSale->location) }}" required maxlength="255" placeholder="Ville, département" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('location') border-red-500 @enderror">
                            @error('location')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description" name="description" required rows="6" maxlength="2000" placeholder="Décrivez votre article en détail..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $urgentSale->description) }}</textarea>
                            <div class="flex justify-between items-center mt-1">
                                @error('description')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @else
                                    <span></span>
                                @enderror
                                <span id="description-count" class="text-sm text-gray-500">0/2000</span>
                            </div>
                        </div>
                        
                        <!-- Photos actuelles -->
                        @if($urgentSale->photos && count($urgentSale->photos) > 0)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Photos actuelles
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="current-photos">
                                    @foreach($urgentSale->photos as $index => $photo)
                                        <div class="relative group" data-photo-index="{{ $index }}">
                                            <img src="{{ Storage::url($photo) }}" alt="Photo {{ $index + 1 }}" class="w-full h-32 object-cover rounded-lg">
                                            <button type="button" onclick="removeCurrentPhoto({{ $index }})" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                            <input type="hidden" name="existing_photos[]" value="{{ $photo }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Nouvelles photos -->
                        <div class="md:col-span-2">
                            <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $urgentSale->photos && count($urgentSale->photos) > 0 ? 'Ajouter de nouvelles photos' : 'Photos' }}
                                @if(!$urgentSale->photos || count($urgentSale->photos) === 0)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                                <label for="photos" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3 block"></i>
                                    <p class="text-gray-600 mb-2">Cliquez pour sélectionner des images</p>
                                    <p class="text-gray-500 text-sm">PNG, JPG, JPEG jusqu'à 5MB chacune (max 10 photos)</p>
                                </label>
                            </div>
                            @error('photos')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('photos.*')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            
                            <!-- Prévisualisation des nouvelles images -->
                            <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 hidden"></div>
                        </div>
                        
                        <!-- Option urgente -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="is_urgent" name="is_urgent" value="1" {{ old('is_urgent', $urgentSale->is_urgent) ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="is_urgent" class="ml-2 block text-sm text-gray-900">
                                    <span class="font-medium">Vente urgente</span>
                                    <span class="text-red-500 ml-1"><i class="fas fa-bolt"></i></span>
                                </label>
                            </div>
                            <p class="text-gray-500 text-sm mt-1">Les ventes urgentes sont mises en avant et apparaissent en premier dans les résultats.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center rounded-b-lg">
                    <a href="{{ route('prestataire.urgent-sales.show', $urgentSale) }}" class="text-gray-600 hover:text-gray-800 font-medium">
                        Annuler
                    </a>
                    
                    <div class="flex gap-3">
                        <button type="submit" name="action" value="draft" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                            <i class="fas fa-save mr-2"></i>Enregistrer en brouillon
                        </button>
                        
                        <button type="submit" name="action" value="publish" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                            <i class="fas fa-check mr-2"></i>Mettre à jour et publier
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
// Compteur de caractères pour la description
const descriptionTextarea = document.getElementById('description');
const descriptionCount = document.getElementById('description-count');

function updateDescriptionCount() {
    const count = descriptionTextarea.value.length;
    descriptionCount.textContent = `${count}/2000`;
    
    if (count > 1800) {
        descriptionCount.classList.add('text-red-500');
        descriptionCount.classList.remove('text-gray-500');
    } else {
        descriptionCount.classList.add('text-gray-500');
        descriptionCount.classList.remove('text-red-500');
    }
}

descriptionTextarea.addEventListener('input', updateDescriptionCount);

// Initialiser le compteur
updateDescriptionCount();

// Prévisualisation des nouvelles images
function previewImages(input) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        preview.classList.remove('hidden');
        
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Nouvelle photo ${index + 1}" class="w-full h-32 object-cover rounded-lg">
                        <button type="button" onclick="removeNewPhoto(${index})" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    `;
                    preview.appendChild(div);
                };
                
                reader.readAsDataURL(file);
            }
        });
    } else {
        preview.classList.add('hidden');
    }
}

// Supprimer une nouvelle photo
function removeNewPhoto(index) {
    const input = document.getElementById('photos');
    const dt = new DataTransfer();
    
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    previewImages(input);
}

// Supprimer une photo actuelle
function removeCurrentPhoto(index) {
    const photoDiv = document.querySelector(`[data-photo-index="${index}"]`);
    if (photoDiv) {
        photoDiv.remove();
    }
}

// Validation du formulaire
document.getElementById('urgent-sale-form').addEventListener('submit', function(e) {
    const currentPhotos = document.querySelectorAll('input[name="existing_photos[]"]').length;
    const newPhotos = document.getElementById('photos').files.length;
    const totalPhotos = currentPhotos + newPhotos;
    
    if (totalPhotos === 0) {
        e.preventDefault();
        alert('Veuillez ajouter au moins une photo.');
        return false;
    }
    
    if (totalPhotos > 10) {
        e.preventDefault();
        alert('Vous ne pouvez pas avoir plus de 10 photos au total.');
        return false;
    }
    
    // Vérifier la taille des nouvelles images
    const files = document.getElementById('photos').files;
    for (let i = 0; i < files.length; i++) {
        if (files[i].size > 5 * 1024 * 1024) { // 5MB
            e.preventDefault();
            alert('Chaque image ne doit pas dépasser 5MB.');
            return false;
        }
    }
});
</script>
@endpush
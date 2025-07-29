@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Modifier un service</h1>
            <a href="{{ route('prestataire.services.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Retour à mes services
            </a>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Oops! Il y a quelques erreurs.</p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('prestataire.services.update', $service->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Titre du service</label>
                <input type="text" name="title" id="title" value="{{ old('title', $service->title) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200" placeholder="Ex: Création de logo professionnel">
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200" placeholder="Décrivez votre service en détail...">{{ old('description', $service->description) }}</textarea>
            </div>

            <div>
                <label for="reservable" class="inline-flex items-center cursor-pointer">
                    <input id="reservable" type="checkbox" name="reservable" value="1" class="rounded h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" {{ old('reservable', $service->reservable) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Activer la réservation directe pour ce service</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Images du service</label>
                <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="image-preview-container">
                    @foreach($service->images as $image)
                        <div class="relative group" id="image-container-{{ $image->id }}">
                            <img src="{{ Storage::url($image->path) }}" alt="Service Image" class="rounded-lg object-cover h-32 w-full">
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <button type="button" data-image-id="{{ $image->id }}" class="delete-image-btn text-white p-2 rounded-full bg-red-500 hover:bg-red-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex items-center justify-center w-full">
                    <label for="images" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3-3 3"></path></svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Cliquez pour ajouter</span> ou glissez-déposez</p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 10MB</p>
                        </div>
                        <input id="images" name="images[]" type="file" class="hidden" multiple onchange="previewImages(event)" />
                    </label>
                </div> 
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catégories</label>
                <div class="relative">
                    <div class="overflow-hidden" id="category-container">
                        <div class="flex space-x-3 transition-transform duration-300 ease-in-out" id="category-list">
                            @foreach($categories as $category)
                            <div class="flex-shrink-0">
                                <input type="checkbox" id="category-{{$category->id}}" name="categories[]" value="{{$category->id}}" class="hidden peer" {{ in_array($category->id, old('categories', $service->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label for="category-{{$category->id}}" class="block cursor-pointer select-none rounded-full px-4 py-2 text-center text-sm font-medium text-gray-600 bg-gray-100 peer-checked:bg-blue-500 peer-checked:text-white transition-colors duration-200 hover:bg-gray-200">
                                    {{ $category->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="delivery_time" class="block text-sm font-semibold text-gray-700 mb-2">Délai de livraison</label>
                    <input type="text" name="delivery_time" id="delivery_time" value="{{ old('delivery_time', $service->delivery_time) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200" placeholder="Ex: 3 jours, 1 semaine">
                </div>
                <div>
                    <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">Localisation</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $service->location) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200" placeholder="Ex: Paris, France">
                </div>
            </div>

            <div class="flex justify-end pt-6">
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                    Mettre à jour le service
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewImages(event) {
    const previewContainer = document.getElementById('image-preview-container');
    const files = event.target.files;
    for (const file of files) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <img src="${e.target.result}" class="rounded-lg object-cover h-32 w-full">
                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <span class="text-white text-xs">Nouvelle image</span>
                </div>
            `;
            previewContainer.appendChild(div);
        }
        reader.readAsDataURL(file);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-image-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const imageId = this.dataset.imageId;
            if (confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
                fetch(`/prestataire/services/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Une erreur est survenue lors de la communication avec le serveur.') });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const imageElement = document.getElementById(`image-container-${imageId}`);
                        if (imageElement) {
                            imageElement.remove();
                        }
                    } else {
                        alert(data.message || 'Erreur lors de la suppression de l\'image.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur: ' + error.message);
                });
            }
        });
    });
});
</script>
@endpush

@endsection
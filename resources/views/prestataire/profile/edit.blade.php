@extends('layouts.app')

@section('title', 'Mon Profil Professionnel')

@section('content')
<div class="py-10">
    <header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">Mon Profil Professionnel</h1>
                    <p class="mt-2 text-sm text-gray-600">Gérez vos informations professionnelles et votre présentation</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('prestataire.profile.preview') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-eye mr-2"></i>
                        Aperçu du profil public
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <main>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Indicateur de complétion -->
                <div class="mb-6 bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Complétion du profil</h3>
                        <span class="text-2xl font-bold text-indigo-600">{{ $completion_percentage ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300" style="width: {{ $completion_percentage ?? 0 }}%"></div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        @if(($completion_percentage ?? 0) < 70)
                            Complétez votre profil pour attirer plus de clients. Un profil complet inspire confiance !
                        @elseif(($completion_percentage ?? 0) < 90)
                            Excellent ! Votre profil est presque complet. Ajoutez quelques détails pour le finaliser.
                        @else
                            Parfait ! Votre profil est complet et prêt à attirer de nouveaux clients.
                        @endif
                    </p>
                </div>
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif
                
                <form action="{{ route('prestataire.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Photo de profil -->
                        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                            <div class="md:grid md:grid-cols-3 md:gap-6">
                                <div class="md:col-span-1">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                                        <i class="fas fa-camera mr-2 text-indigo-600"></i>
                                        Photo de profil
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">Une photo claire et professionnelle inspire confiance aux clients.</p>
                                </div>
                                <div class="mt-5 md:mt-0 md:col-span-2">
                                    <div class="flex items-center space-x-6">
                                        @if($prestataire && $prestataire->photo)
                <img class="h-24 w-24 rounded-full object-cover border-4 border-indigo-100" src="{{ asset('storage/' . $prestataire->photo) }}" alt="Photo actuelle">
                @else
                                            <div class="h-24 w-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center border-4 border-indigo-100">
                                                <span class="text-2xl font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <input type="file" name="photo" id="photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                            <p class="mt-1 text-xs text-gray-500">Format recommandé : JPEG, PNG. Taille max : 2MB</p>
                                            @if($prestataire->user->profile_photo_url)
                                                <button type="button" onclick="deletePhoto()" class="mt-2 text-sm text-red-600 hover:text-red-500">Supprimer la photo</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations de base -->
                        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                            <div class="md:grid md:grid-cols-3 md:gap-6">
                                <div class="md:col-span-1">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                                        <i class="fas fa-user mr-2 text-indigo-600"></i>
                                        Informations personnelles
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">Ces informations seront visibles par les clients.</p>
                                </div>
                                <div class="mt-5 md:mt-0 md:col-span-2">
                                    <div class="grid grid-cols-6 gap-6">
                                        <!-- Nom -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="name" class="block text-sm font-medium text-gray-700">Nom complet *</label>
                                            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                        </div>
                                        
                                        <!-- Email -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                        </div>
                                        
                                        <!-- Téléphone -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $prestataire->phone ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        
                                        <!-- Secteur d'activité -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="sector" class="block text-sm font-medium text-gray-700">Secteur d'activité</label>
                                            <select name="sector" id="sector" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                <option value="">Sélectionnez un secteur</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->name }}" {{ old('sector', $prestataire->sector ?? '') === $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Présentation / Biographie -->
                        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                            <div class="md:grid md:grid-cols-3 md:gap-6">
                                <div class="md:col-span-1">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                                        <i class="fas fa-edit mr-2 text-indigo-600"></i>
                                        Présentation professionnelle
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">Décrivez votre expertise, votre expérience et ce qui vous différencie. Minimum 200 caractères.</p>
                                </div>
                                <div class="mt-5 md:mt-0 md:col-span-2">
                                    <div>
                                        <textarea name="description" id="description" rows="6" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Présentez votre expertise, votre expérience, vos points forts et votre manière de travailler...">{{ old('description', $prestataire->description ?? '') }}</textarea>
                                        <div class="mt-2 flex justify-between items-center">
                                            <p class="text-sm text-gray-500">Cette description améliore l'aspect humain de votre profil et favorise la confiance client.</p>
                                            <span id="char-count" class="text-sm text-gray-400">0/2000</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Compétences -->
                        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                            <div class="md:grid md:grid-cols-3 md:gap-6">
                                <div class="md:col-span-1">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                                        <i class="fas fa-tools mr-2 text-indigo-600"></i>
                                        Compétences et expertise
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">Sélectionnez vos compétences principales. Elles servent de base aux filtres de recherche.</p>
                                </div>
                                <div class="mt-5 md:mt-0 md:col-span-2">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            @foreach($skills as $skill)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="skills[]" value="{{ $skill->id }}" 
                                                           {{ $prestataire && $prestataire->skills->contains($skill->id) ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $skill->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Zone de service -->
                                        <div class="pt-4 border-t">
                                            <div>
                                                <label for="service_area" class="block text-sm font-medium text-gray-700">Zone de service</label>
                                                <input type="text" name="service_area" id="service_area" value="{{ old('service_area', $prestataire->service_area ?? '') }}" placeholder="Ex: Paris et banlieue" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Portfolio -->
                        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                            <div class="md:grid md:grid-cols-3 md:gap-6">
                                <div class="md:col-span-1">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                                        <i class="fas fa-images mr-2 text-indigo-600"></i>
                                        Portfolio de réalisations
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">Montrez vos meilleurs travaux. Le portfolio est un critère déterminant pour la sélection.</p>
                                </div>
                                <div class="mt-5 md:mt-0 md:col-span-2">
                                    <!-- Portfolio existant -->
                                    @if($prestataire && is_array($prestataire->portfolio_images) && count($prestataire->portfolio_images) > 0)
                                        <div class="mb-6">
                                            <h4 class="text-sm font-medium text-gray-900 mb-3">Réalisations actuelles</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @foreach($prestataire->portfolio_images as $index => $item)
                                                    <div class="border rounded-lg p-4 bg-gray-50">
                                                        @if(isset($item['image']))
                                                            <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['title'] ?? 'Portfolio item' }}" class="w-full h-32 object-cover rounded mb-2">
                                                        @endif
                                                        <h5 class="font-medium text-sm">{{ $item['title'] ?? 'Sans titre' }}</h5>
                                                        <p class="text-xs text-gray-600 mt-1">{{ $item['description'] ?? '' }}</p>
                                                        @if(isset($item['link']) && $item['link'])
                                                            <a href="{{ $item['link'] }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-500">Voir le projet</a>
                                                        @endif
                                                        <button type="button" onclick="deletePortfolioItem({{ $index }})" class="mt-2 text-xs text-red-600 hover:text-red-500">Supprimer</button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Ajouter de nouveaux éléments -->
                                    <div id="portfolio-container">
                                        <h4 class="text-sm font-medium text-gray-900 mb-3">Ajouter de nouvelles réalisations</h4>
                                        <div class="portfolio-item border-2 border-dashed border-gray-300 rounded-lg p-4">
                                            <div class="grid grid-cols-1 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Image</label>
                                                    <input type="file" name="portfolio_images[]" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                </div>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Titre</label>
                                                        <input type="text" name="portfolio_titles[]" placeholder="Nom du projet" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Lien (optionnel)</label>
                                                        <input type="url" name="portfolio_links[]" placeholder="https://" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                                    <textarea name="portfolio_descriptions[]" rows="2" placeholder="Décrivez brièvement ce projet..." class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" onclick="addPortfolioItem()" class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <i class="fas fa-plus mr-2"></i>
                                        Ajouter une réalisation
                                    </button>
                                    <p class="mt-2 text-xs text-gray-500">Maximum 10 réalisations. Taille max par image : 5MB</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('prestataire.dashboard') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Annuler
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
// Compteur de caractères pour la description
document.getElementById('description').addEventListener('input', function() {
    const count = this.value.length;
    const counter = document.getElementById('char-count');
    counter.textContent = count + '/2000';
    
    if (count < 200) {
        counter.className = 'text-sm text-red-500';
    } else if (count > 1800) {
        counter.className = 'text-sm text-orange-500';
    } else {
        counter.className = 'text-sm text-green-500';
    }
});

// Initialiser le compteur
document.addEventListener('DOMContentLoaded', function() {
    const description = document.getElementById('description');
    if (description.value) {
        description.dispatchEvent(new Event('input'));
    }
});

// Ajouter un élément de portfolio
function addPortfolioItem() {
    const container = document.getElementById('portfolio-container');
    const items = container.querySelectorAll('.portfolio-item');
    
    if (items.length >= 10) {
        alert('Vous ne pouvez ajouter que 10 réalisations maximum.');
        return;
    }
    
    const newItem = items[0].cloneNode(true);
    // Vider les champs
    newItem.querySelectorAll('input, textarea').forEach(input => input.value = '');
    
    // Ajouter un bouton de suppression
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'mt-2 text-sm text-red-600 hover:text-red-500';
    removeBtn.innerHTML = '<i class="fas fa-trash mr-1"></i>Supprimer';
    removeBtn.onclick = function() { newItem.remove(); };
    
    newItem.appendChild(removeBtn);
    container.appendChild(newItem);
}

// Supprimer la photo de profil
function deletePhoto() {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
        fetch('{{ route('prestataire.profile.delete-photo') }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}

// Supprimer un élément du portfolio
function deletePortfolioItem(index) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette réalisation ?')) {
        const url = '{{ route('prestataire.profile.delete-portfolio-item', ':index') }}'.replace(':index', index);
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
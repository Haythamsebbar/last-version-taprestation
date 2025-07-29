@extends('layouts.app')

@section('title', 'Parcourir les prestataires')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">🔍 Parcourir les prestataires</h1>
                    <p class="text-gray-600">Trouvez le prestataire parfait pour vos besoins</p>
                </div>
                <div class="mt-4 lg:mt-0">
                    <a href="{{ route('client.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        ← Retour au tableau de bord
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('client.browse.prestataires') }}" class="space-y-4">
                <!-- Barre de recherche -->
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                        <input type="text" name="search" id="search" value="{{ $search }}" 
                               placeholder="Nom, compétences, description..." 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex-shrink-0 flex items-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            🔍 Rechercher
                        </button>
                    </div>
                </div>

                <!-- Filtres avancés -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                        <select name="category" id="category" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Localisation</label>
                        <input type="text" name="location" id="location" value="{{ $location }}" 
                               placeholder="Ville, région..." 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="min_rating" class="block text-sm font-medium text-gray-700 mb-1">Note minimum</label>
                        <select name="min_rating" id="min_rating" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Toutes les notes</option>
                            <option value="4" {{ $minRating == 4 ? 'selected' : '' }}>4+ étoiles</option>
                            <option value="3" {{ $minRating == 3 ? 'selected' : '' }}>3+ étoiles</option>
                            <option value="2" {{ $minRating == 2 ? 'selected' : '' }}>2+ étoiles</option>
                        </select>
                    </div>

                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Trier par</label>
                        <select name="sort" id="sort" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>Note (décroissant)</option>
                            <option value="reviews_count" {{ $sort === 'reviews_count' ? 'selected' : '' }}>Nombre d'avis</option>
                            <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Nom (A-Z)</option>
                            <option value="created_at" {{ $sort === 'created_at' ? 'selected' : '' }}>Plus récents</option>
                        </select>
                    </div>
                </div>

                <!-- Filtres de prix supprimés pour des raisons de confidentialité -->
            </form>
        </div>

        <!-- Statistiques de recherche -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">{{ $prestataires->total() }}</span> prestataire(s) trouvé(s)
                    @if($search || $category || $location || $minRating)
                        <span class="ml-2">
                            - <a href="{{ route('client.browse.prestataires') }}" class="text-blue-600 hover:text-blue-800">Effacer les filtres</a>
                        </span>
                    @endif
                </div>
                <div class="mt-2 sm:mt-0">
                    <span class="text-sm text-gray-500">Page {{ $prestataires->currentPage() }} sur {{ $prestataires->lastPage() }}</span>
                </div>
            </div>
        </div>

        <!-- Liste des prestataires -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
            @forelse($prestataires as $prestataire)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Photo de profil -->
                    <div class="relative h-48 bg-gradient-to-br from-blue-500 to-purple-600">
                        @if($prestataire->photo)
                            <img src="{{ Storage::url($prestataire->photo) }}" 
                                 alt="{{ $prestataire->user->prenom }} {{ $prestataire->user->nom }}" 
                                 class="w-full h-full object-cover">
                        @elseif($prestataire->user->avatar)
                            <img src="{{ Storage::url($prestataire->user->avatar) }}" 
                                 alt="{{ $prestataire->user->prenom }} {{ $prestataire->user->nom }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Badge de vérification -->
                        @if($prestataire->isVerified())
                            <div class="absolute top-3 left-3">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Badge de statut -->
                        @if($prestataire->user->is_online)
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    🟢 En ligne
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="p-6">
                        <!-- Nom et titre -->
                        <div class="mb-3">
                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold text-gray-900 mr-2">
                                    {{ $prestataire->user->prenom }} {{ $prestataire->user->nom }}
                                </h3>
                                @if($prestataire->isVerified())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Vérifié
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">{{ $prestataire->titre_professionnel }}</p>
                        </div>

                        <!-- Note et avis -->
                        <div class="flex items-center mb-3">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($prestataire->average_rating))
                                        <span class="text-yellow-400">⭐</span>
                                    @elseif($i - 0.5 <= $prestataire->average_rating)
                                        <span class="text-yellow-400">⭐</span>
                                    @else
                                        <span class="text-gray-300">⭐</span>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-600">
                                {{ number_format($prestataire->average_rating, 1) }} 
                                ({{ $prestataire->reviews_count }} avis)
                            </span>
                        </div>

                        <!-- Description -->
                        <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                            {{ Str::limit($prestataire->description, 120) }}
                        </p>

                        <!-- Compétences -->
                        @if($prestataire->competences->count() > 0)
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($prestataire->competences->take(3) as $competence)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $competence->nom }}
                                        </span>
                                    @endforeach
                                    @if($prestataire->competences->count() > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            +{{ $prestataire->competences->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Prix et localisation -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-sm text-gray-600">
                                📍 {{ $prestataire->user->ville ?? 'Non spécifié' }}
                            </div>
                            <!-- Tarif horaire supprimé pour des raisons de confidentialité -->
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('client.browse.prestataires.show', $prestataire) }}" 
                               class="flex-1 text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Voir le profil
                            </a>
                            @auth
                                @if(auth()->user()->hasRole('client'))
                                    <button onclick="toggleFollow({{ $prestataire->id }})" 
                                            id="follow-btn-{{ $prestataire->id }}"
                                            class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors
                                                   {{ $prestataire->isFollowedBy(auth()->user()) ? 'bg-red-50 border-red-300 text-red-600' : 'text-gray-600' }}">
                                        @if($prestataire->isFollowedBy(auth()->user()))
                                            💔
                                        @else
                                            ❤️
                                        @endif
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-gray-400 text-2xl">🔍</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun prestataire trouvé</h3>
                        <p class="text-gray-500 mb-4">Aucun prestataire ne correspond à vos critères de recherche.</p>
                        <a href="{{ route('client.browse.prestataires') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Voir tous les prestataires
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($prestataires->hasPages())
            <div class="bg-white rounded-lg shadow-md p-6">
                {{ $prestataires->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@auth
@if(auth()->user()->hasRole('client'))
<script>
function toggleFollow(prestataireId) {
    const btn = document.getElementById(`follow-btn-${prestataireId}`);
    const isFollowing = btn.classList.contains('bg-red-50');
    
    fetch(`/client/prestataires/${prestataireId}/follow`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            action: isFollowing ? 'unfollow' : 'follow'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.following) {
                btn.classList.add('bg-red-50', 'border-red-300', 'text-red-600');
                btn.innerHTML = '💔';
            } else {
                btn.classList.remove('bg-red-50', 'border-red-300', 'text-red-600');
                btn.innerHTML = '❤️';
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}
</script>
@endif
@endauth
@endsection
@extends('layouts.app')

@section('title', $urgentSale->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <a href="{{ route('prestataire.urgent-sales.index') }}" class="text-red-600 hover:text-red-800 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-red-800">{{ $urgentSale->title }}</h1>
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($urgentSale->status === 'active') bg-green-100 text-green-800
                            @elseif($urgentSale->status === 'sold') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $urgentSale->status_label }}
                        </span>
                        
                        @if($urgentSale->is_urgent)
                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-bolt mr-1"></i>URGENT
                            </span>
                        @endif
                        
                        <span class="text-gray-500 text-sm">
                            Publié le {{ $urgentSale->created_at->format('d/m/Y à H:i') }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3">
                @if($urgentSale->canBeEdited())
                    <a href="{{ route('prestataire.urgent-sales.edit', $urgentSale) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                @endif
                
                @if($urgentSale->status === 'active')
                    <form action="{{ route('prestataire.urgent-sales.update-status', $urgentSale) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="sold">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200" onclick="return confirm('Marquer cette vente comme vendue ?')">
                            <i class="fas fa-check mr-2"></i>Marquer comme vendu
                        </button>
                    </form>
                @endif
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                        <div class="py-1">
                            @if($urgentSale->status === 'inactive')
                                <form action="{{ route('prestataire.urgent-sales.update-status', $urgentSale) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-play mr-2"></i>Activer
                                    </button>
                                </form>
                            @elseif($urgentSale->status === 'active')
                                <form action="{{ route('prestataire.urgent-sales.update-status', $urgentSale) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="inactive">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-pause mr-2"></i>Désactiver
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('urgent-sales.show', $urgentSale) }}" target="_blank" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-external-link-alt mr-2"></i>Voir en public
                            </a>
                            
                            <form action="{{ route('prestataire.urgent-sales.destroy', $urgentSale) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-trash mr-2"></i>Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne principale -->
            <div class="lg:col-span-2">
                <!-- Photos -->
                @if($urgentSale->photos && count($urgentSale->photos) > 0)
                    <div class="bg-white rounded-lg shadow mb-6">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Photos</h2>
                            
                            <!-- Photo principale -->
                            <div class="mb-4">
                                <img id="main-image" src="{{ Storage::url($urgentSale->photos[0]) }}" alt="{{ $urgentSale->title }}" class="w-full h-96 object-cover rounded-lg">
                            </div>
                            
                            <!-- Miniatures -->
                            @if(count($urgentSale->photos) > 1)
                                <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                                    @foreach($urgentSale->photos as $index => $photo)
                                        <img src="{{ Storage::url($photo) }}" alt="Photo {{ $index + 1 }}" class="w-full h-16 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity {{ $index === 0 ? 'ring-2 ring-blue-500' : '' }}" onclick="changeMainImage('{{ Storage::url($photo) }}', this)">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Description -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
                        <div class="prose max-w-none">
                            {!! nl2br(e($urgentSale->description)) !!}
                        </div>
                    </div>
                </div>
                
                <!-- Statistiques détaillées -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistiques</h2>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $urgentSale->views_count }}</div>
                                <div class="text-sm text-gray-600">Vues</div>
                            </div>
                            
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $urgentSale->contact_count }}</div>
                                <div class="text-sm text-gray-600">Contacts</div>
                            </div>
                            
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $urgentSale->created_at->diffForHumans() }}</div>
                                <div class="text-sm text-gray-600">En ligne depuis</div>
                            </div>
                            
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $urgentSale->updated_at->diffForHumans() }}</div>
                                <div class="text-sm text-gray-600">Dernière modification</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Informations principales -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-medium text-gray-600">Prix</span>
                                <div class="text-3xl font-bold text-red-600">{{ number_format($urgentSale->price, 0, ',', ' ') }} €</div>
                            </div>
                            
                            <div>
                                <span class="text-sm font-medium text-gray-600">État</span>
                                <div class="text-lg font-semibold text-gray-900">{{ $urgentSale->condition_label }}</div>
                            </div>
                            
                            <div>
                                <span class="text-sm font-medium text-gray-600">Quantité</span>
                                <div class="text-lg font-semibold text-gray-900">{{ $urgentSale->quantity }}</div>
                            </div>
                            
                            <div>
                                <span class="text-sm font-medium text-gray-600">Localisation</span>
                                <div class="text-lg font-semibold text-gray-900">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>{{ $urgentSale->location }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contacts reçus -->
                @if($urgentSale->contact_count > 0)
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-semibold text-gray-900">Contacts reçus</h2>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-semibold">{{ $urgentSale->contact_count }}</span>
                            </div>
                            
                            <a href="{{ route('prestataire.urgent-sales.contacts', $urgentSale) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-lg transition duration-200 block">
                                <i class="fas fa-envelope mr-2"></i>Voir tous les contacts
                            </a>
                            
                            @if($recentContacts->count() > 0)
                                <div class="mt-4 space-y-3">
                                    <h3 class="text-sm font-medium text-gray-600">Contacts récents</h3>
                                    @foreach($recentContacts as $contact)
                                        <div class="border border-gray-200 rounded-lg p-3">
                                            <div class="flex justify-between items-start mb-2">
                                                <span class="font-medium text-gray-900">{{ $contact->user->name }}</span>
                                                <span class="text-xs text-gray-500">{{ $contact->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 line-clamp-2">{{ $contact->message }}</p>
                                            @if($contact->status === 'pending')
                                                <span class="inline-block mt-2 bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">
                                                    En attente
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 text-center">
                            <i class="fas fa-envelope text-gray-400 text-3xl mb-3"></i>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun contact</h3>
                            <p class="text-gray-600 text-sm">Vous n'avez pas encore reçu de contact pour cette vente.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function changeMainImage(src, element) {
    document.getElementById('main-image').src = src;
    
    // Retirer la bordure de toutes les miniatures
    document.querySelectorAll('.grid img').forEach(img => {
        img.classList.remove('ring-2', 'ring-blue-500');
    });
    
    // Ajouter la bordure à la miniature cliquée
    element.classList.add('ring-2', 'ring-blue-500');
}
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

.prose {
    line-height: 1.6;
}
</style>
@endpush
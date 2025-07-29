@extends('layouts.app')

@section('title', 'Détails de la demande')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Session Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">Succès</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Erreur</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <p class="font-bold">Attention</p>
                <p>{{ session('warning') }}</p>
            </div>
        @endif

        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('client.equipment-rental-requests.index') }}" class="text-gray-700 hover:text-blue-600">
                        Mes demandes
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">Demande #{{ $request->id }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- En-tête -->
        <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        Demande de location #{{ $request->id }}
                    </h1>
                    <div class="flex items-center space-x-4">
                        @if($request->status === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            En attente de réponse
                        </span>
                        @elseif($request->status === 'accepted')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Acceptée
                        </span>
                        @elseif($request->status === 'rejected')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            Refusée
                        </span>
                        @endif
                        
                        <span class="text-sm text-gray-500">
                            Demandée le {{ $request->created_at->format('d/m/Y à H:i') }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 md:mt-0 flex space-x-3">
                    @if($request->status === 'pending')
                    <form method="POST" 
                          action="{{ route('client.equipment-rental-requests.destroy', $request) }}" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                            Annuler la demande
                        </button>
                    </form>
                    @endif
                    
                    @if($request->status === 'accepted')
                    <a href="{{ route('client.equipment-rentals.index') }}" 
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Voir mes locations
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations de l'équipement -->
                <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Équipement demandé</h2>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            @if($request->equipment->photos && count($request->equipment->photos) > 0)
                            <img src="{{ Storage::url($request->equipment->photos[0]) }}" 
                                 alt="{{ $request->equipment->name }}"
                                 class="w-24 h-24 object-cover rounded-lg cursor-pointer"
                                 onclick="showPhotoModal('{{ Storage::url($request->equipment->photos[0]) }}', '{{ $request->equipment->name }}')">
                            @else
                            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                <a href="{{ route('equipment.show', $request->equipment) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $request->equipment->name }}
                                </a>
                            </h3>
                            <div class="space-y-1 text-sm text-gray-600">
                                @if($request->equipment->brand || $request->equipment->model)
                                <p><span class="font-medium">Marque/Modèle:</span> {{ $request->equipment->brand }} {{ $request->equipment->model }}</p>
                                @endif
                                <p><span class="font-medium">État:</span> {{ $request->equipment->formatted_condition }}</p>
                                <p><span class="font-medium">Prix journalier:</span> {{ number_format($request->equipment->daily_rate, 0) }}€</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Détails de la demande -->
                <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Détails de la demande</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Période de location</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date de début:</span>
                                    <span class="font-medium">{{ $request->start_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date de fin:</span>
                                    <span class="font-medium">{{ $request->end_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Durée:</span>
                                    <span class="font-medium">{{ $request->duration_days }} jour{{ $request->duration_days > 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Options de livraison</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Livraison demandée:</span>
                                    <span class="font-medium">
                                        @if($request->delivery_required)
                                        <span class="text-green-600">Oui</span>
                                        @else
                                        <span class="text-gray-500">Non</span>
                                        @endif
                                    </span>
                                </div>
                                @if($request->delivery_required)
                                <div class="mt-3">
                                    <span class="text-gray-600 block mb-1">Adresse de livraison:</span>
                                    <div class="bg-gray-50 p-3 rounded-lg text-sm">
                                        {{ $request->delivery_address }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($request->message)
                    <div class="mt-6">
                        <h3 class="font-medium text-gray-900 mb-3">Message</h3>
                        <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-700">
                            {{ $request->message }}
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Réponse du prestataire -->
                @if($request->status !== 'pending')
                <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        @if($request->status === 'accepted')
                        Demande acceptée
                        @else
                        Demande refusée
                        @endif
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Date de réponse:</span>
                            <span class="font-medium">{{ $request->updated_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        
                        @if($request->provider_message)
                        <div>
                            <span class="text-gray-600 text-sm block mb-2">Message du prestataire:</span>
                            <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-700">
                                {{ $request->provider_message }}
                            </div>
                        </div>
                        @endif
                        
                        @if($request->status === 'accepted')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-green-800 font-medium">Votre demande a été acceptée !</span>
                            </div>
                            <p class="text-green-700 text-sm mt-2">
                                Une location a été créée automatiquement. Vous pouvez la consulter dans votre espace "Mes locations".
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Historique -->
                <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Historique</h2>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Demande créée</p>
                                <p class="text-xs text-gray-500">{{ $request->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($request->status !== 'pending')
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 {{ $request->status === 'accepted' ? 'bg-green-500' : 'bg-red-500' }} rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    Demande {{ $request->status === 'accepted' ? 'acceptée' : 'refusée' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $request->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Récapitulatif financier -->
                <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Location ({{ $request->duration_days }} jour{{ $request->duration_days > 1 ? 's' : '' }}):</span>
                            <span class="font-medium">{{ number_format($request->rental_amount, 0) }}€</span>
                        </div>
                        
                        @if($request->delivery_required && $request->delivery_cost > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Livraison:</span>
                            <span class="font-medium">{{ number_format($request->delivery_cost, 0) }}€</span>
                        </div>
                        @endif
                        
                        @if($request->equipment->deposit_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Caution:</span>
                            <span class="font-medium">{{ number_format($request->equipment->deposit_amount, 0) }}€</span>
                        </div>
                        @endif
                        
                        <hr class="my-3">
                        
                        <div class="flex justify-between text-lg font-semibold">
                            <span class="text-gray-900">Total:</span>
                            <span class="text-blue-600">{{ number_format($request->total_amount, 0) }}€</span>
                        </div>
                        
                        @if($request->equipment->deposit_amount > 0)
                        <p class="text-xs text-gray-500 mt-2">
                            * La caution sera restituée après retour de l'équipement en bon état
                        </p>
                        @endif
                    </div>
                </div>
                
                <!-- Informations du prestataire -->
                <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Prestataire</h3>
                    <div class="flex items-start space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-bold">
                                {{ substr($request->equipment->prestataire->company_name ?? $request->equipment->prestataire->first_name, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">
                                {{ $request->equipment->prestataire->company_name ?? $request->equipment->prestataire->first_name . ' ' . $request->equipment->prestataire->last_name }}
                            </h4>
                            @if($request->equipment->prestataire->address)
                            <p class="text-sm text-gray-600 mt-1">{{ $request->equipment->prestataire->address }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4 space-y-2">
                        <a href="mailto:{{ $request->equipment->prestataire->email }}" 
                           class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 text-center block">
                            Contacter par email
                        </a>
                        
                        @if($request->equipment->prestataire->phone)
                        <a href="tel:{{ $request->equipment->prestataire->phone }}" 
                           class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 text-center block">
                            Appeler
                        </a>
                        @endif
                    </div>
                </div>
                
                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-md border border-blue-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                    <div class="space-y-3">
                        <a href="{{ route('equipment.show', $request->equipment) }}" 
                           class="w-full px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 text-center block">
                            Voir l'équipement
                        </a>
                        
                        <a href="{{ route('equipment.index') }}" 
                           class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 text-center block">
                            Parcourir le matériel
                        </a>
                        
                        @if($request->status === 'rejected')
                        <a href="{{ route('equipment.show', $request->equipment) }}" 
                           class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 text-center block">
                            Nouvelle demande
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal pour afficher les photos -->
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="max-w-4xl max-h-full p-4">
        <img id="modalPhoto" src="" alt="" class="max-w-full max-h-full object-contain">
        <button onclick="closePhotoModal()" 
                class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            ×
        </button>
    </div>
</div>

<script>
// Gestion des photos
function showPhotoModal(photoUrl, altText) {
    const modal = document.getElementById('photoModal');
    const modalPhoto = document.getElementById('modalPhoto');
    
    modalPhoto.src = photoUrl;
    modalPhoto.alt = altText;
    modal.classList.remove('hidden');
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.add('hidden');
}

// Fermer la modal en cliquant à l'extérieur
document.getElementById('photoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

// Fermer la modal avec la touche Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('photoModal').classList.add('hidden');
    }
});
</script>
@endsection
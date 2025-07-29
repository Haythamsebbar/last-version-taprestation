@extends('layouts.app')

@section('title', 'Location #' . $rental->id)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('prestataire.equipment-rentals.index') }}" 
                       class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Location #{{ $rental->id }}</h1>
                        <p class="text-gray-600">{{ $rental->equipment->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($rental->status === 'pending_start') bg-yellow-100 text-yellow-800
                        @elseif($rental->status === 'active') bg-green-100 text-green-800
                        @elseif($rental->status === 'completed') bg-blue-100 text-blue-800
                        @elseif($rental->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        @if($rental->status === 'pending_start') À démarrer
                        @elseif($rental->status === 'active') En cours
                        @elseif($rental->status === 'completed') Terminée
                        @elseif($rental->status === 'cancelled') Annulée
                        @else {{ ucfirst($rental->status) }} @endif
                    </span>
                    
                    @if($rental->status === 'pending_start')
                    <form method="POST" action="{{ route('prestataire.equipment-rentals.start', $rental) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200"
                                onclick="return confirm('Démarrer cette location ?')">
                            ▶️ Démarrer
                        </button>
                    </form>
                    @endif
                    
                    @if($rental->status === 'active')
                    <form method="POST" action="{{ route('prestataire.equipment-rentals.complete', $rental) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200"
                                onclick="return confirm('Marquer cette location comme terminée ?')">
                            ✅ Terminer
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations du client -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">👤 Client</h2>
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-lg">
                                {{ substr($rental->client->first_name, 0, 1) }}{{ substr($rental->client->last_name, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ $rental->client->first_name }} {{ $rental->client->last_name }}</h3>
                            <div class="mt-2 space-y-1">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Email:</span> 
                                    <a href="mailto:{{ $rental->client->email }}" class="text-blue-600 hover:text-blue-800">{{ $rental->client->email }}</a>
                                </p>
                                @if($rental->client->phone)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Téléphone:</span> 
                                    <a href="tel:{{ $rental->client->phone }}" class="text-blue-600 hover:text-blue-800">{{ $rental->client->phone }}</a>
                                </p>
                                @endif
                                @if($rental->client->address)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Adresse:</span> {{ $rental->client->address }}
                                </p>
                                @endif
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Membre depuis:</span> {{ $rental->client->created_at->format('F Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Détails de l'équipement -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Équipement loué</h2>
                    <div class="flex items-start space-x-4">
                        @if($rental->equipment->main_photo)
                        <div class="w-32 h-32 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="{{ Storage::url($rental->equipment->main_photo) }}" 
                                 alt="{{ $rental->equipment->name }}"
                                 class="w-full h-full object-cover cursor-pointer"
                                 onclick="showPhotoModal('{{ Storage::url($rental->equipment->main_photo) }}', '{{ $rental->equipment->name }}')">
                        </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ $rental->equipment->name }}</h3>
                            <p class="text-gray-600 mb-3">{{ $rental->equipment->brand }} {{ $rental->equipment->model }}</p>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">État:</span>
                                    <span class="text-gray-600">{{ $rental->equipment->formatted_condition }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Prix/jour:</span>
                                    <span class="text-gray-600">{{ number_format($rental->equipment->daily_rate, 2) }}€</span>
                                </div>
                                @if($rental->equipment->serial_number)
                                <div>
                                    <span class="font-medium text-gray-700">N° série:</span>
                                    <span class="text-gray-600 font-mono">{{ $rental->equipment->serial_number }}</span>
                                </div>
                                @endif
                                @if($rental->equipment->weight)
                                <div>
                                    <span class="font-medium text-gray-700">Poids:</span>
                                    <span class="text-gray-600">{{ $rental->equipment->weight }} kg</span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="mt-3">
                                <a href="{{ route('prestataire.equipment.show', $rental->equipment) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Voir la fiche complète →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Détails de la location -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Détails de la location</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Période</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Début:</span>
                                    <span class="font-medium">{{ $rental->start_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Fin prévue:</span>
                                    <span class="font-medium">{{ $rental->end_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Durée:</span>
                                    <span class="font-medium">{{ $rental->start_date->diffInDays($rental->end_date) + 1 }} jour(s)</span>
                                </div>
                                @if($rental->actual_end_date)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Fin réelle:</span>
                                    <span class="font-medium">{{ $rental->actual_end_date->format('d/m/Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Options</h3>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    @if($rental->delivery_required)
                                        <span class="text-green-600">✅</span>
                                        <span class="text-sm">Livraison incluse</span>
                                    @else
                                        <span class="text-gray-400">❌</span>
                                        <span class="text-sm text-gray-600">Récupération sur place</span>
                                    @endif
                                </div>
                                
                                @if($rental->delivery_required && $rental->delivery_address)
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Adresse de livraison:</span><br>
                                    {{ $rental->delivery_address }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($rental->notes)
                    <div class="mt-6">
                        <h3 class="font-medium text-gray-900 mb-2">Notes</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700">{{ $rental->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Problèmes signalés -->
                @if($rental->problem_reported)
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-red-900 mb-4">⚠️ Problème signalé</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="font-medium text-red-800">Date du signalement:</span>
                            <span class="text-red-700">{{ $rental->problem_reported_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @if($rental->problem_description)
                        <div>
                            <span class="font-medium text-red-800">Description:</span>
                            <div class="mt-2 bg-white rounded-lg p-4 border border-red-200">
                                <p class="text-red-700">{{ $rental->problem_description }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Historique -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📅 Historique</h2>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Location créée</p>
                                <p class="text-xs text-gray-500">{{ $rental->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($rental->started_at)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Location démarrée</p>
                                <p class="text-xs text-gray-500">{{ $rental->started_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($rental->problem_reported_at)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Problème signalé</p>
                                <p class="text-xs text-gray-500">{{ $rental->problem_reported_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($rental->completed_at)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Location terminée</p>
                                <p class="text-xs text-gray-500">{{ $rental->completed_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Récapitulatif financier -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">💰 Récapitulatif</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Location ({{ $rental->start_date->diffInDays($rental->end_date) + 1 }} jours):</span>
                            <span class="font-medium">{{ number_format($rental->rental_amount, 2) }}€</span>
                        </div>
                        
                        @if($rental->delivery_required && $rental->delivery_cost > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Livraison:</span>
                            <span class="font-medium">{{ number_format($rental->delivery_cost, 2) }}€</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <span class="font-medium text-gray-900">Total:</span>
                            <span class="font-bold text-lg text-blue-600">{{ number_format($rental->total_amount, 2) }}€</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Caution:</span>
                            <span class="font-medium text-gray-900">{{ number_format($rental->deposit_amount, 2) }}€</span>
                        </div>
                        
                        @if($rental->status === 'completed')
                        <div class="flex justify-between items-center text-green-600">
                            <span class="font-medium">Revenus nets:</span>
                            <span class="font-bold">{{ number_format($rental->total_amount, 2) }}€</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">⚡ Actions</h2>
                    <div class="space-y-3">
                        @if($rental->status === 'pending_start')
                        <form method="POST" action="{{ route('prestataire.equipment-rentals.start', $rental) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200"
                                    onclick="return confirm('Démarrer cette location ?')">
                                ▶️ Démarrer la location
                            </button>
                        </form>
                        @endif
                        
                        @if($rental->status === 'active')
                        <form method="POST" action="{{ route('prestataire.equipment-rentals.complete', $rental) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200"
                                    onclick="return confirm('Marquer cette location comme terminée ?')">
                                ✅ Terminer la location
                            </button>
                        </form>
                        @endif
                        
                        @if(in_array($rental->status, ['pending_start', 'active']) && !$rental->problem_reported)
                        <button type="button" 
                                onclick="reportProblem()"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                            ⚠️ Signaler un problème
                        </button>
                        @endif
                        
                        <a href="mailto:{{ $rental->client->email }}" 
                           class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200 text-center block">
                            📧 Contacter le client
                        </a>
                        
                        @if($rental->client->phone)
                        <a href="tel:{{ $rental->client->phone }}" 
                           class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200 text-center block">
                            📞 Appeler
                        </a>
                        @endif
                    </div>
                </div>
                
                <!-- Informations complémentaires -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">ℹ️ Informations</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Location créée:</span><br>
                            <span class="text-gray-600">{{ $rental->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        
                        <div>
                            <span class="font-medium text-gray-700">Dernière mise à jour:</span><br>
                            <span class="text-gray-600">{{ $rental->updated_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        
                        <div>
                            <span class="font-medium text-gray-700">Référence:</span><br>
                            <span class="text-gray-600 font-mono">#{{ $rental->id }}</span>
                        </div>
                        
                        @if($rental->rental_request_id)
                        <div>
                            <span class="font-medium text-gray-700">Demande originale:</span><br>
                            <a href="{{ route('prestataire.equipment-rental-requests.show', $rental->rental_request_id) }}" 
                               class="text-blue-600 hover:text-blue-800 font-mono">
                                #{{ $rental->rental_request_id }}
                            </a>
                        </div>
                        @endif
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
            ✕
        </button>
    </div>
</div>

<!-- Modal pour signaler un problème -->
<div id="problemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Signaler un problème</h3>
            <form method="POST" action="{{ route('prestataire.equipment-rentals.report-problem', $rental) }}">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="problem_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description du problème *
                    </label>
                    <textarea id="problem_description" 
                              name="problem_description" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Décrivez le problème rencontré..."></textarea>
                </div>
                
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="closeProblemModal()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition-colors duration-200">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Signaler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showPhotoModal(photoUrl, altText) {
    const modal = document.getElementById('photoModal');
    const modalPhoto = document.getElementById('modalPhoto');
    
    modalPhoto.src = photoUrl;
    modalPhoto.alt = altText;
    modal.classList.remove('hidden');
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.add('hidden');
}

function reportProblem() {
    const modal = document.getElementById('problemModal');
    modal.classList.remove('hidden');
}

function closeProblemModal() {
    const modal = document.getElementById('problemModal');
    const form = modal.querySelector('form');
    
    modal.classList.add('hidden');
    form.reset();
}

// Fermer les modals en cliquant à l'extérieur
document.getElementById('photoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePhotoModal();
    }
});

document.getElementById('problemModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeProblemModal();
    }
});

// Fermer les modals avec la touche Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoModal();
        closeProblemModal();
    }
});
</script>
@endsection
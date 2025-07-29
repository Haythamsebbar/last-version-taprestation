@extends('layouts.app')

@section('title', $equipment->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('prestataire.equipment.index') }}" 
                       class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $equipment->name }}</h1>
                        <p class="text-gray-600">{{ $equipment->brand }} {{ $equipment->model }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($equipment->availability_status === 'available') bg-green-100 text-green-800
                        @elseif($equipment->availability_status === 'rented') bg-yellow-100 text-yellow-800
                        @elseif($equipment->availability_status === 'maintenance') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $equipment->formatted_availability_status }}
                    </span>
                    <a href="{{ route('prestataire.equipment.edit', $equipment) }}" 
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                        ✏️ Modifier
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Photo -->
                @if($equipment->main_photo)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">🖼️ Image principale</h2>
                    <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden">
                        <img src="{{ Storage::url($equipment->main_photo) }}" alt="{{ $equipment->name }}" 
                             class="w-full h-full object-cover">
                    </div>
                </div>
                @endif

                <!-- Photos -->
                @if($equipment->photos && count($equipment->photos) > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📸 Photos</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($equipment->photos as $photo)
                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                            <img src="{{ Storage::url($photo) }}" alt="{{ $equipment->name }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-200 cursor-pointer"
                                 onclick="openPhotoModal('{{ Storage::url($photo) }}')">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Description -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Description</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $equipment->description }}</p>
                </div>
                
                <!-- Détails techniques -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">🔧 Détails techniques</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($equipment->brand)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Marque:</span>
                            <span class="text-gray-900">{{ $equipment->brand }}</span>
                        </div>
                        @endif
                        
                        @if($equipment->model)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Modèle:</span>
                            <span class="text-gray-900">{{ $equipment->model }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">État:</span>
                            <span class="text-gray-900">{{ $equipment->formatted_condition }}</span>
                        </div>
                        
                        @if($equipment->weight)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Poids:</span>
                            <span class="text-gray-900">{{ $equipment->weight }} kg</span>
                        </div>
                        @endif
                        
                        @if($equipment->dimensions)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Dimensions:</span>
                            <span class="text-gray-900">{{ $equipment->dimensions }}</span>
                        </div>
                        @endif
                        
                        @if($equipment->power_requirements)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">Alimentation:</span>
                            <span class="text-gray-900">{{ $equipment->power_requirements }}</span>
                        </div>
                        @endif
                        
                        @if($equipment->serial_number)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-600">N° de série:</span>
                            <span class="text-gray-900">{{ $equipment->serial_number }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Accessoires et instructions -->
                @if($equipment->accessories_included || $equipment->usage_instructions || $equipment->safety_instructions)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📖 Instructions et accessoires</h2>
                    
                    @if($equipment->accessories_included)
                    <div class="mb-4">
                        <h3 class="font-medium text-gray-900 mb-2">Accessoires inclus:</h3>
                        <p class="text-gray-700">{{ $equipment->accessories_included }}</p>
                    </div>
                    @endif
                    
                    @if($equipment->usage_instructions)
                    <div class="mb-4">
                        <h3 class="font-medium text-gray-900 mb-2">Instructions d'utilisation:</h3>
                        <p class="text-gray-700">{{ $equipment->usage_instructions }}</p>
                    </div>
                    @endif
                    
                    @if($equipment->safety_instructions)
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">⚠️ Consignes de sécurité:</h3>
                        <p class="text-gray-700 bg-yellow-50 p-3 rounded-lg border border-yellow-200">{{ $equipment->safety_instructions }}</p>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Demandes de location récentes -->
                @if($equipment->rentalRequests->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">📋 Demandes de location récentes</h2>
                        <a href="{{ route('prestataire.equipment-rental-requests.index', ['equipment_id' => $equipment->id]) }}" 
                           class="text-green-600 hover:text-green-800 text-sm font-medium">
                            Voir toutes
                        </a>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($equipment->rentalRequests->take(5) as $request)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 font-medium text-sm">{{ substr($request->client->first_name, 0, 1) }}{{ substr($request->client->last_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $request->client->first_name }} {{ $request->client->last_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $request->start_date->format('d/m/Y') }} - {{ $request->end_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($request->status === 'accepted') bg-green-100 text-green-800
                                    @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($request->total_amount, 2) }}€</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Avis -->
                @if($equipment->reviews->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">⭐ Avis clients</h2>
                        <div class="flex items-center space-x-2">
                            <span class="text-yellow-500">⭐</span>
                            <span class="font-medium">{{ number_format($equipment->average_rating, 1) }}/5</span>
                            <span class="text-gray-600">({{ $equipment->reviews_count }} avis)</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($equipment->reviews->take(3) as $review)
                        <div class="border-b border-gray-100 pb-4 last:border-b-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900">{{ $review->client->first_name }} {{ substr($review->client->last_name, 0, 1) }}.</span>
                                    <div class="flex text-yellow-500">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                ⭐
                                            @else
                                                ☆
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($review->comment)
                            <p class="text-gray-700">{{ $review->comment }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Tarification -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">💰 Tarification</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Prix/jour:</span>
                            <span class="font-bold text-lg text-green-600">{{ number_format($equipment->price_per_day, 2) }}€</span>
                        </div>
                        
                        @if($equipment->price_per_week)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Prix/semaine:</span>
                            <span class="font-medium text-green-600">{{ number_format($equipment->price_per_week, 2) }}€</span>
                        </div>
                        @endif
                        
                        @if($equipment->price_per_month)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Prix/mois:</span>
                            <span class="font-medium text-green-600">{{ number_format($equipment->price_per_month, 2) }}€</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <span class="text-gray-600">Caution:</span>
                            <span class="font-medium text-gray-900">{{ number_format($equipment->security_deposit, 2) }}€</span>
                        </div>
                    </div>
                </div>
                
                <!-- Conditions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📝 Conditions</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Durée min:</span>
                            <span class="text-gray-900">{{ $equipment->minimum_rental_duration }} jour(s)</span>
                        </div>
                        
                        @if($equipment->maximum_rental_duration)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Durée max:</span>
                            <span class="text-gray-900">{{ $equipment->maximum_rental_duration }} jour(s)</span>
                        </div>
                        @endif
                        
                        @if($equipment->minimum_age)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Âge min:</span>
                            <span class="text-gray-900">{{ $equipment->minimum_age }} ans</span>
                        </div>
                        @endif
                        
                        @if($equipment->requires_license)
                        <div class="flex items-center space-x-2 text-orange-600">
                            <span>⚠️</span>
                            <span class="text-sm">Permis requis</span>
                        </div>
                        @endif
                        
                        @if($equipment->insurance_required)
                        <div class="flex items-center space-x-2 text-green-600">
                            <span>🛡️</span>
                            <span class="text-sm">Assurance requise</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Livraison -->
                @if($equipment->delivery_included)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">🚚 Livraison</h2>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 text-green-600">
                            <span>✅</span>
                            <span class="text-sm">Livraison incluse</span>
                        </div>
                        
                        @if($equipment->delivery_radius)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Rayon:</span>
                            <span class="text-gray-900">{{ $equipment->delivery_radius }} km</span>
                        </div>
                        @endif
                        
                        @if($equipment->delivery_fee)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Coût:</span>
                            <span class="text-gray-900">{{ number_format($equipment->delivery_fee, 2) }}€</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Catégories -->
                @if($equipment->categories->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">🏷️ Catégories</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($equipment->categories as $category)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            {{ $category->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Statistiques -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Statistiques</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Locations:</span>
                            <span class="font-medium text-gray-900">{{ $equipment->rentals->count() }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Demandes:</span>
                            <span class="font-medium text-gray-900">{{ $equipment->rentalRequests->count() }}</span>
                        </div>
                        
                        @if($equipment->reviews->count() > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Note moyenne:</span>
                            <span class="font-medium text-gray-900">{{ number_format($equipment->average_rating, 1) }}/5</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Créé le:</span>
                            <span class="text-gray-900">{{ $equipment->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">⚡ Actions rapides</h2>
                    <div class="space-y-3">
                        <a href="{{ route('prestataire.equipment.edit', $equipment) }}" 
                           class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 text-center block">
                            ✏️ Modifier
                        </a>
                        
                        @if($equipment->is_active)
                        <form method="POST" action="{{ route('prestataire.equipment.toggle-status', $equipment) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors duration-200">
                                ⏸️ Désactiver
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('prestataire.equipment.toggle-status', $equipment) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                                ▶️ Activer
                            </button>
                        </form>
                        @endif
                        
                        <button onclick="confirmDelete()" 
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                            🗑️ Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les photos -->
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
    <div class="max-w-4xl max-h-full p-4">
        <img id="modalPhoto" src="" alt="" class="max-w-full max-h-full object-contain">
        <button onclick="closePhotoModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            ✕
        </button>
    </div>
</div>

<!-- Formulaire de suppression caché -->
<form id="deleteForm" method="POST" action="{{ route('prestataire.equipment.destroy', $equipment) }}" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function openPhotoModal(photoUrl) {
        document.getElementById('modalPhoto').src = photoUrl;
        document.getElementById('photoModal').classList.remove('hidden');
    }
    
    function closePhotoModal() {
        document.getElementById('photoModal').classList.add('hidden');
    }
    
    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet équipement ? Cette action est irréversible.')) {
            document.getElementById('deleteForm').submit();
        }
    }
    
    // Fermer le modal en cliquant en dehors
    document.getElementById('photoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePhotoModal();
        }
    });
    
    // Fermer le modal avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePhotoModal();
        }
    });
</script>
@endpush
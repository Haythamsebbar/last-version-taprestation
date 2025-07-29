@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('title', $equipment->name . ' - Location de matériel - TaPrestation')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-green-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Accueil
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('equipment.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-green-600 md:ml-2">Matériel à louer</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ Str::limit($equipment->name, 50) }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Image and details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    @if($equipment->main_photo)
                        <div class="relative">
                            <div class="aspect-w-16 aspect-h-12">
                                <img id="mainImage" src="{{ Storage::url($equipment->main_photo) }}" alt="{{ $equipment->name }}" class="w-full h-96 object-cover">
                            </div>
                            <div class="absolute top-4 left-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                Disponible
                            </div>
                        </div>
                    @else
                        <div class="h-96 bg-gray-200 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-500">Aucune photo disponible</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
                    <div class="prose max-w-none text-gray-700">
                        {!! nl2br(e($equipment->description)) !!}
                    </div>
                </div>

                <!-- Informations détaillées -->
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations détaillées</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        @if($equipment->brand)
                            <div>
                                <span class="font-medium text-gray-900">Marque :</span>
                                <span class="text-gray-700">{{ $equipment->brand }}</span>
                            </div>
                        @endif
                        
                        @if($equipment->model)
                            <div>
                                <span class="font-medium text-gray-900">Modèle :</span>
                                <span class="text-gray-700">{{ $equipment->model }}</span>
                            </div>
                        @endif
                        
                        @if($equipment->condition)
                            <div>
                                <span class="font-medium text-gray-900">État :</span>
                                <span class="text-gray-700">{{ $equipment->formatted_condition }}</span>
                            </div>
                        @endif
                        
                        @if($equipment->weight)
                            <div>
                                <span class="font-medium text-gray-900">Poids :</span>
                                <span class="text-gray-700">{{ $equipment->weight }} kg</span>
                            </div>
                        @endif
                        
                        @if($equipment->dimensions)
                            <div>
                                <span class="font-medium text-gray-900">Dimensions :</span>
                                <span class="text-gray-700">{{ $equipment->dimensions }}</span>
                            </div>
                        @endif
                        
                        @if($equipment->power_requirements)
                            <div>
                                <span class="font-medium text-gray-900">Alimentation :</span>
                                <span class="text-gray-700">{{ $equipment->power_requirements }}</span>
                            </div>
                        @endif
                        
                        @if($equipment->minimum_age)
                            <div>
                                <span class="font-medium text-gray-900">Âge minimum :</span>
                                <span class="text-gray-700">{{ $equipment->minimum_age }} ans</span>
                            </div>
                        @endif
                        
                        @if($equipment->requires_license)
                            <div>
                                <span class="font-medium text-gray-900">Permis requis :</span>
                                <span class="text-red-600">{{ $equipment->required_license_type ?? 'Oui' }}</span>
                            </div>
                        @endif
                    </div>
                    
                    @if($equipment->minimum_rental_duration || $equipment->maximum_rental_duration)
                        <div class="border-t pt-4 mb-4">
                            <h3 class="font-medium text-gray-900 mb-2">Durée de location</h3>
                            <div class="space-y-1 text-sm text-gray-700">
                                @if($equipment->minimum_rental_duration)
                                    <div>Durée minimum : {{ $equipment->minimum_rental_duration }} jour(s)</div>
                                @endif
                                @if($equipment->maximum_rental_duration)
                                    <div>Durée maximum : {{ $equipment->maximum_rental_duration }} jour(s)</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Spécifications techniques -->
                @if($equipment->technical_specifications)
                    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Spécifications techniques</h2>
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($equipment->technical_specifications)) !!}
                        </div>
                    </div>
                @endif
                 
                 <!-- Accessoires -->
                 @if($equipment->included_accessories || $equipment->optional_accessories)
                     <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                         <h2 class="text-xl font-semibold text-gray-900 mb-4">Accessoires</h2>
                         
                         @if($equipment->included_accessories && count($equipment->included_accessories) > 0)
                             <div class="mb-4">
                                 <h3 class="font-medium text-gray-900 mb-2">Inclus dans la location</h3>
                                 <ul class="list-disc list-inside space-y-1 text-gray-700">
                                     @foreach($equipment->included_accessories as $accessory)
                                         <li>{{ $accessory }}</li>
                                     @endforeach
                                 </ul>
                             </div>
                         @endif
                         
                         @if($equipment->optional_accessories && count($equipment->optional_accessories) > 0)
                             <div>
                                 <h3 class="font-medium text-gray-900 mb-2">Accessoires optionnels</h3>
                                 <ul class="list-disc list-inside space-y-1 text-gray-700">
                                     @foreach($equipment->optional_accessories as $accessory)
                                         <li>{{ $accessory }}</li>
                                     @endforeach
                                 </ul>
                             </div>
                         @endif
                     </div>
                 @endif
                 
                 <!-- Conditions de location -->
                 @if($equipment->rental_conditions)
                     <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                         <h2 class="text-xl font-semibold text-gray-900 mb-4">Conditions de location</h2>
                         <div class="prose max-w-none text-gray-700">
                             {!! nl2br(e($equipment->rental_conditions)) !!}
                         </div>
                     </div>
                 @endif
                 
                 <!-- Instructions d'utilisation -->
                 @if($equipment->usage_instructions)
                     <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                         <h2 class="text-xl font-semibold text-gray-900 mb-4">Instructions d'utilisation</h2>
                         <div class="prose max-w-none text-gray-700">
                             {!! nl2br(e($equipment->usage_instructions)) !!}
                         </div>
                     </div>
                 @endif
                 
                 <!-- Instructions de sécurité -->
                 @if($equipment->safety_instructions)
                     <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                         <h2 class="text-xl font-semibold text-gray-900 mb-4">Instructions de sécurité</h2>
                         <div class="prose max-w-none text-gray-700 bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
                             <div class="flex items-start">
                                 <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                 </svg>
                                 <div>{!! nl2br(e($equipment->safety_instructions)) !!}</div>
                             </div>
                         </div>
                     </div>
                 @endif
             </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $equipment->name }}</h1>
                        
                        <!-- Prix de location -->
                        <div class="space-y-2">
                            @if($equipment->price_per_day)
                                <div class="text-2xl font-bold text-green-600">{{ number_format($equipment->price_per_day, 2) }}€ / jour</div>
                            @endif
                            
                            @if($equipment->price_per_hour)
                                <div class="text-lg text-gray-700">{{ number_format($equipment->price_per_hour, 2) }}€ / heure</div>
                            @endif
                            
                            @if($equipment->price_per_week)
                                <div class="text-lg text-gray-700">{{ number_format($equipment->price_per_week, 2) }}€ / semaine</div>
                            @endif
                            
                            @if($equipment->price_per_month)
                                <div class="text-lg text-gray-700">{{ number_format($equipment->price_per_month, 2) }}€ / mois</div>
                            @endif
                        </div>
                        
                        <!-- Caution et frais -->
                        @if($equipment->security_deposit || $equipment->delivery_fee)
                            <div class="mt-4 space-y-1 text-sm text-gray-600">
                                @if($equipment->security_deposit)
                                    <div>Caution : {{ number_format($equipment->security_deposit, 2) }}€</div>
                                @endif
                                @if($equipment->delivery_fee && !$equipment->delivery_included)
                                    <div>Frais de livraison : {{ number_format($equipment->delivery_fee, 2) }}€</div>
                                @elseif($equipment->delivery_included)
                                    <div class="text-green-600">✓ Livraison incluse</div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Prestataire Info -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Propriétaire</h3>
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                @if($equipment->prestataire && $equipment->prestataire->photo)
                                    <img src="{{ Storage::url($equipment->prestataire->photo) }}" alt="{{ $equipment->prestataire->user->name ?? '' }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <span class="text-lg font-medium text-gray-600">{{ substr($equipment->prestataire->user->name ?? '', 0, 1) }}</span>
                                @endif
                            </div>
                            <div>
                                <a href="#" class="font-medium text-gray-900 hover:text-green-600">{{ $equipment->prestataire->user->name ?? '' }}</a>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                @if($equipment->city)
                                    {{ $equipment->city }}
                                    @if($equipment->postal_code), {{ $equipment->postal_code }}@endif
                                @else
                                    {{ $equipment->address ?? 'Localisation non spécifiée' }}
                                @endif
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Publié {{ $equipment->created_at->diffForHumans() }}
                            </div>
                            @if($equipment->view_count)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ $equipment->view_count }} vue(s)
                                </div>
                            @endif
                            @if($equipment->total_rentals)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $equipment->total_rentals }} location(s) réalisée(s)
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location Map -->
                    @if ($equipment->latitude && $equipment->longitude)
                        <div class="border-t border-gray-200 pt-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Localisation sur carte</h3>
                            <div id="map" style="height: 250px;" class="rounded-lg z-10"></div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="border-t border-gray-200 pt-6">
                        <a href="{{ route('equipment.reserve', $equipment) }}" class="w-full bg-green-600 text-white text-center font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition duration-300 mb-3 block">Réserver cet équipement</a>
                        <button onclick="openReportModal()" class="w-full bg-red-600 text-white text-center font-bold py-2 px-4 rounded-lg hover:bg-red-700 transition duration-300">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Signaler cet équipement
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Equipment -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Équipements similaires</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @if(isset($similarEquipment) && $similarEquipment->count() > 0)
                    @foreach($similarEquipment as $item)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <a href="{{ route('equipment.show', $item) }}">
                                <img src="{{ $item->photo_url }}" alt="{{ $item->name }}" class="h-48 w-full object-cover">
                            </a>
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2"><a href="{{ route('equipment.show', $item) }}">{{ $item->name }}</a></h3>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($item->description, 75) }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-green-600">{{ number_format($item->daily_rate, 2) }}€/jour</span>
                                    <a href="{{ route('equipment.show', $item) }}" class="text-green-600 hover:text-green-800 font-semibold">Voir</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500">Aucun équipement similaire trouvé.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de signalement -->
<div id="reportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Signaler cet équipement</h3>
                    <button onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form action="{{ route('equipment.report', $equipment) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Catégorie du signalement</label>
                        <select id="category" name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="inappropriate">Contenu inapproprié</option>
                            <option value="fraud">Annonce frauduleuse</option>
                            <option value="safety">Problème de sécurité</option>
                            <option value="condition">État de l'équipement</option>
                            <option value="pricing">Prix incorrect</option>
                            <option value="availability">Disponibilité incorrecte</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Raison du signalement</label>
                        <input type="text" id="reason" name="reason" required maxlength="255"
                               placeholder="Résumé du problème..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description détaillée</label>
                        <textarea id="description" name="description" rows="3" required minlength="20" maxlength="1000"
                                  placeholder="Décrivez le problème en détail (minimum 20 caractères)..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeReportModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition duration-200">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">
                            Signaler
                        </button>
                    </div>

                    <!-- Contact et Réservation -->
                    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact et Réservation</h3>
                        
                        @if($equipment->user)
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                    @if($equipment->user->avatar)
                                        <img src="{{ asset('storage/' . $equipment->user->avatar) }}" alt="{{ $equipment->user->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $equipment->user->name }}</h4>
                                    @if($equipment->user->company_name)
                                        <p class="text-sm text-gray-600">{{ $equipment->user->company_name }}</p>
                                    @endif
                                    @if($equipment->user->rating)
                                        <div class="flex items-center mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $equipment->user->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                            <span class="ml-1 text-sm text-gray-600">({{ $equipment->user->rating }})</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($equipment->phone)
                            <div class="flex items-center mb-3">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-gray-700">{{ $equipment->phone }}</span>
                            </div>
                        @endif

                        @if($equipment->email)
                            <div class="flex items-center mb-4">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-700">{{ $equipment->email }}</span>
                            </div>
                        @endif

                        @if($equipment->availability_status)
                            <div class="mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($equipment->availability_status === 'available') bg-green-100 text-green-800
                                    @elseif($equipment->availability_status === 'rented') bg-red-100 text-red-800
                                    @elseif($equipment->availability_status === 'maintenance') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if($equipment->availability_status === 'available')
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Disponible
                                    @elseif($equipment->availability_status === 'rented')
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Loué
                                    @elseif($equipment->availability_status === 'maintenance')
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        En maintenance
                                    @else
                                        {{ ucfirst($equipment->availability_status) }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if($equipment->available_from && $equipment->available_until)
                            <div class="mb-4">
                                <h5 class="font-medium text-gray-900 mb-2">Période de disponibilité</h5>
                                <p class="text-sm text-gray-600">
                                    Du {{ \Carbon\Carbon::parse($equipment->available_from)->format('d/m/Y') }} 
                                    au {{ \Carbon\Carbon::parse($equipment->available_until)->format('d/m/Y') }}
                                </p>
                            </div>
                        @endif

                        <div class="space-y-3">
                            <button class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                Réserver maintenant
                            </button>
                            <button class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 transition duration-200">
                                Contacter le propriétaire
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if ($equipment->latitude && $equipment->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    var map = L.map('map').setView([{{ $equipment->latitude }}, {{ $equipment->longitude }}], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([{{ $equipment->latitude }}, {{ $equipment->longitude }}]).addTo(map)
        .bindPopup('Localisation approximative de l\'équipement.')
        .openPopup();
</script>
@endif

<script>
    function openReportModal() {
        document.getElementById('reportModal').classList.remove('hidden');
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.add('hidden');
    }

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('reportModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReportModal();
        }
    });
</script>
@endpush
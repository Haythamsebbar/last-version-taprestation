@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('prestataire.equipment.show', $equipment) }}" class="text-gray-600 hover:text-gray-800 mr-4">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Modifier l'équipement</h1>
            <p class="mt-1 text-sm text-gray-600">Modifiez les informations de votre équipement</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prestataire.equipment.update', $equipment) }}" method="POST" enctype="multipart/form-data" class="mt-8">
        @csrf
        @method('PUT')

        <!-- Formulaire d'édition -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="space-y-6">
                    <!-- Nom de l'équipement -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'équipement *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $equipment->name) }}" required 
                               placeholder="Ex: Perceuse sans fil Bosch" 
                               class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Catégories -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégories *</label>
                        <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 rounded-md p-3">
                            @foreach ($categories as $category)
                                <div>
                                    <label class="font-medium text-gray-800">{{ $category->name }}</label>
                                    @if($category->children->count() > 0)
                                        <div class="ml-4 space-y-1 mt-1">
                                            @foreach ($category->children as $child)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="categories[]" value="{{ $child->id }}" 
                                                           {{ in_array($child->id, old('categories', $equipment->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                                           class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $child->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="ml-4">
                                            <label class="flex items-center">
                                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                                       {{ in_array($category->id, old('categories', $equipment->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                                <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description courte *</label>
                        <textarea id="description" name="description" rows="3" required 
                                  placeholder="Décrivez brièvement votre équipement, son état et ses caractéristiques principales" 
                                  class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">{{ old('description', $equipment->description) }}</textarea>
                    </div>

                    <!-- Spécifications techniques -->
                    <div>
                        <label for="technical_specifications" class="block text-sm font-medium text-gray-700">Spécifications techniques</label>
                        <textarea id="technical_specifications" name="technical_specifications" rows="3" 
                                  placeholder="Dimensions, poids, puissance, capacité..." 
                                  class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">{{ old('technical_specifications', $equipment->technical_specifications) }}</textarea>
                    </div>

                    <!-- Photo principale actuelle -->
                    @if($equipment->main_photo || (is_array($equipment->photos) && count($equipment->photos) > 0))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Photo principale actuelle</label>
                            <div class="flex items-center space-x-4">
                                @if($equipment->main_photo)
                                    <img src="{{ Storage::url($equipment->main_photo) }}" alt="Photo principale" class="w-20 h-20 object-cover rounded-lg">
                                @elseif(is_array($equipment->photos) && count($equipment->photos) > 0)
                                    <img src="{{ Storage::url($equipment->photos[0]) }}" alt="Photo principale" class="w-20 h-20 object-cover rounded-lg">
                                @endif
                                <span class="text-sm text-gray-500">Photo actuelle</span>
                            </div>
                        </div>
                    @endif

                    <!-- Nouvelle photo principale -->
                    <div>
                        <label for="main_photo" class="block text-sm font-medium text-gray-700">Nouvelle photo principale</label>
                        <input type="file" name="main_photo" id="main_photo" accept="image/*" 
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-600 hover:file:bg-green-100">
                        <p class="mt-1 text-xs text-gray-500">Laissez vide pour conserver la photo actuelle</p>
                    </div>

                    <!-- Photos supplémentaires actuelles -->
                    @if(is_array($equipment->photos) && count($equipment->photos) > 1)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Photos supplémentaires actuelles</label>
                            <div class="grid grid-cols-4 gap-4">
                                @foreach(array_slice($equipment->photos, 1) as $photo)
                                    <img src="{{ Storage::url($photo) }}" alt="Photo supplémentaire" class="w-20 h-20 object-cover rounded-lg">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Nouvelles photos supplémentaires -->
                    <div>
                        <label for="photos" class="block text-sm font-medium text-gray-700">Nouvelles photos supplémentaires</label>
                        <input type="file" name="photos[]" id="photos" accept="image/*" multiple 
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-600 hover:file:bg-green-100">
                        <p class="mt-1 text-xs text-gray-500">Ajoutez de nouvelles photos (les anciennes seront conservées)</p>
                    </div>

                    <!-- Prix par jour -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="daily_rate" class="block text-sm font-medium text-gray-700">Prix par jour (€) *</label>
                            <input type="number" name="daily_rate" id="daily_rate" value="{{ old('daily_rate', $equipment->daily_rate) }}" required 
                                   min="0" step="0.01" placeholder="50" 
                                   class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="deposit_amount" class="block text-sm font-medium text-gray-700">Caution (€) *</label>
                            <input type="number" name="deposit_amount" id="deposit_amount" value="{{ old('deposit_amount', $equipment->deposit_amount) }}" required 
                                   min="0" step="0.01" placeholder="100" 
                                   class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>

                    <!-- Autres prix -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="price_per_hour" class="block text-sm font-medium text-gray-700">Prix par heure (€)</label>
                            <input type="number" name="price_per_hour" id="price_per_hour" step="0.01" min="0" 
                                   class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                                   placeholder="10.00" value="{{ old('price_per_hour', $equipment->price_per_hour) }}">
                        </div>
                        <div>
                            <label for="price_per_week" class="block text-sm font-medium text-gray-700">Prix par semaine (€)</label>
                            <input type="number" name="price_per_week" id="price_per_week" step="0.01" min="0" 
                                   class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                                   placeholder="300.00" value="{{ old('price_per_week', $equipment->price_per_week) }}">
                        </div>
                        <div>
                            <label for="price_per_month" class="block text-sm font-medium text-gray-700">Prix par mois (€)</label>
                            <input type="number" name="price_per_month" id="price_per_month" step="0.01" min="0" 
                                   class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                                   placeholder="1000.00" value="{{ old('price_per_month', $equipment->price_per_month) }}">
                        </div>
                    </div>

                    <!-- Livraison disponible -->
                    <div>
                        <div class="flex items-center">
                            <input id="delivery_available" name="delivery_available" type="checkbox" value="1" {{ old('delivery_available', $equipment->delivery_available) ? 'checked' : '' }} 
                                   class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <label for="delivery_available" class="ml-2 block text-sm text-gray-900">Livraison disponible</label>
                        </div>
                    </div>

                    <!-- Coût de livraison -->
                    <div>
                        <label for="delivery_cost" class="block text-sm font-medium text-gray-700">Coût de livraison (€)</label>
                        <input type="number" name="delivery_cost" id="delivery_cost" step="0.01" min="0" 
                               class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                               placeholder="0.00" value="{{ old('delivery_cost', $equipment->delivery_cost) }}">
                    </div>

                    <!-- Disponibilité -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="available_from" class="block text-sm font-medium text-gray-700 mb-2">
                                Disponible à partir de
                            </label>
                            <input type="date" name="available_from" id="available_from" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   value="{{ old('available_from', $equipment->available_from ? $equipment->available_from->format('Y-m-d') : '') }}">
                            @error('available_from')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="available_until" class="block text-sm font-medium text-gray-700 mb-2">
                                Disponible jusqu'au
                            </label>
                            <input type="date" name="available_until" id="available_until" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   value="{{ old('available_until', $equipment->available_until ? $equipment->available_until->format('Y-m-d') : '') }}">
                            @error('available_until')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Accessoires et conditions -->
                    <div class="mb-4">
                        <label for="accessories" class="block text-sm font-medium text-gray-700 mb-2">
                            Accessoires inclus
                        </label>
                        <textarea name="accessories" id="accessories" rows="2" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                placeholder="Casques, gants, manuel d'utilisation...">{{ old('accessories', $equipment->accessories) }}</textarea>
                        @error('accessories')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="rental_conditions" class="block text-sm font-medium text-gray-700 mb-2">
                            Conditions de location
                        </label>
                        <textarea name="rental_conditions" id="rental_conditions" rows="3" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                placeholder="Conditions particulières, restrictions d'usage...">{{ old('rental_conditions', $equipment->rental_conditions) }}</textarea>
                        @error('rental_conditions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Exigences -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="license_required" id="license_required" value="1" 
                                   {{ old('license_required', $equipment->license_required) ? 'checked' : '' }}
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="license_required" class="ml-2 block text-sm text-gray-900">
                                Permis ou certification requis
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_available" id="is_available" value="1" 
                                   {{ old('is_available', $equipment->is_available) ? 'checked' : '' }}
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="is_available" class="ml-2 block text-sm text-gray-900">
                                Équipement disponible immédiatement
                            </label>
                        </div>
                    </div>

                    <!-- État et statut -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                                État de l'équipement
                            </label>
                            <select name="condition" id="condition" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Sélectionner l'état</option>
                                <option value="excellent" {{ old('condition', $equipment->condition) == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="very_good" {{ old('condition', $equipment->condition) == 'very_good' ? 'selected' : '' }}>Très bon</option>
                                <option value="good" {{ old('condition', $equipment->condition) == 'good' ? 'selected' : '' }}>Bon</option>
                                <option value="fair" {{ old('condition', $equipment->condition) == 'fair' ? 'selected' : '' }}>Correct</option>
                                <option value="poor" {{ old('condition', $equipment->condition) == 'poor' ? 'selected' : '' }}>Mauvais</option>
                            </select>
                            @error('condition')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Statut
                            </label>
                            <select name="status" id="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="active" {{ old('status', $equipment->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="inactive" {{ old('status', $equipment->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                <option value="maintenance" {{ old('status', $equipment->status) == 'maintenance' ? 'selected' : '' }}>En maintenance</option>
                                <option value="rented" {{ old('status', $equipment->status) == 'rented' ? 'selected' : '' }}>Loué</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Localisation détaillée -->
                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse complète
                        </label>
                        <input type="text" name="address" id="address" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="123 Rue de la République" value="{{ old('address', $equipment->address) }}">
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Ville <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="city" id="city" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Paris" value="{{ old('city', $equipment->city) }}" required>
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Code postal
                            </label>
                            <input type="text" name="postal_code" id="postal_code" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="75001" value="{{ old('postal_code', $equipment->postal_code) }}">
                            @error('postal_code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                Pays <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="country" id="country" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="France" value="{{ old('country', $equipment->country) }}" required>
                            @error('country')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-3 pt-6">
            <a href="{{ route('prestataire.equipment.show', $equipment) }}" 
               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Annuler
            </a>
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
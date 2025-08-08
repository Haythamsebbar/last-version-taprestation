@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register-form.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
.user-type-selector {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.user-type-option {
    flex: 1;
    padding: 1.5rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    background: white;
}

.user-type-option:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.user-type-option.selected {
    border-color: #3b82f6;
    background: #eff6ff;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.user-type-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #6b7280;
}

.user-type-option.selected .user-type-icon {
    color: #3b82f6;
}

.user-type-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #1f2937;
}

.user-type-description {
    font-size: 0.875rem;
    color: #6b7280;
}

.form-section {
    display: none;
}

.form-section.active {
    display: block;
}

.map-container {
    margin-top: 0.5rem;
}

#clientRegistrationMap, #prestataireRegistrationMap {
    height: 16rem;
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
}

/* Styles pour les sections d'importation de photo de profil */
.file-input-wrapper {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-control-file {
    width: 100%;
    padding: 0.75rem;
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
    background-color: #f9fafb;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.875rem;
    color: #6b7280;
}

.form-control-file:hover {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

.form-control-file:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.file-name-display {
    padding: 0.5rem;
    background-color: #f3f4f6;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    color: #374151;
    min-height: 1.5rem;
    display: flex;
    align-items: center;
    border: 1px solid #e5e7eb;
}

.file-name-display:empty::before {
    content: "Aucun fichier sélectionné";
    color: #9ca3af;
    font-style: italic;
}

.file-input-wrapper .form-control-file::-webkit-file-upload-button {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    margin-right: 0.75rem;
    font-size: 0.875rem;
    transition: background-color 0.2s;
}

.file-input-wrapper .form-control-file::-webkit-file-upload-button:hover {
    background: #2563eb;
}

.file-input-wrapper .form-control-file::file-selector-button {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    margin-right: 0.75rem;
    font-size: 0.875rem;
    transition: background-color 0.2s;
}

.file-input-wrapper .form-control-file::file-selector-button:hover {
    background: #2563eb;
}

/* Styles pour les sélecteurs de catégories */
.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    background-color: white;
    font-size: 0.875rem;
    color: #374151;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control:hover {
    border-color: #9ca3af;
}

.form-control option {
    padding: 0.5rem;
    color: #374151;
}

.form-control option:hover {
    background-color: #f3f4f6;
}

#subcategory-group {
    transition: all 0.3s ease;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush

@section('content')
<div class="register-container">
    <div class="reassurance-banner">
        <i class="fas fa-check-circle"></i> <span id="reassurance-text">Choisissez votre type de compte pour commencer</span>
    </div>
    
    <div class="register-card">
        <div>
            <h2 class="register-title">
                Créer votre compte
            </h2>
            <p class="register-subtitle">
                Rejoignez TaPrestation en tant que client ou prestataire
            </p>
        </div>
        
        <!-- Sélecteur de type d'utilisateur -->
        <div class="user-type-selector">
            <div class="user-type-option" data-type="client">
                <div class="user-type-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-type-title">Client</div>
                <div class="user-type-description">Je cherche des services professionnels</div>
            </div>
            
            <div class="user-type-option" data-type="prestataire">
                <div class="user-type-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="user-type-title">Prestataire</div>
                <div class="user-type-description">Je propose mes services professionnels</div>
            </div>
        </div>
        
        @if ($errors->any())
            <div class="error-container">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Formulaire Client -->
        <div id="client-form" class="form-section">
            <form id="client-form-element" class="mt-8 space-y-6" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_type" value="client">
                
                <!-- Section: Informations de connexion -->
                <div class="mb-8">
                    <h3 class="section-header">Informations de connexion</h3>
                    
                    <div class="space-y-4">
                        <div class="form-group">
                            <label for="client_name" class="form-label">Nom complet</label>
                            <input id="client_name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" class="form-control" placeholder="Votre nom complet">
                        </div>
                        
                        <div class="form-group">
                            <label for="client_email" class="form-label">E-mail</label>
                            <input id="client_email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="form-control" placeholder="votre@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="client_password" class="form-label">Mot de passe</label>
                            <input id="client_password" name="password" type="password" autocomplete="new-password" required class="form-control" placeholder="Minimum 8 caractères">
                        </div>
                        
                        <div class="form-group">
                            <label for="client_password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input id="client_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="form-control" placeholder="Confirmez votre mot de passe">
                        </div>
                    </div>
                </div>
                
                <!-- Section: Informations personnelles -->
                <div>
                    <h3 class="section-header">Informations personnelles</h3>
                    
                    <div class="space-y-4">
                        <div class="form-group">
                            <label for="location" class="form-label">Localisation *</label>
                            <div class="map-container">
                                <div id="clientRegistrationMap" class="h-64 rounded-lg border border-gray-300"></div>
                                <div class="mt-3">
                                    <input type="text" id="selectedAddress" name="location" value="{{ old('location') }}" class="form-control" placeholder="Cliquez sur la carte pour sélectionner votre localisation" readonly>
                                    <input type="hidden" id="selectedLatitude" name="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" id="selectedLongitude" name="longitude" value="{{ old('longitude') }}">
                                    <div class="flex gap-3 mt-3">
                                        <button type="button" id="getCurrentLocationBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Ma position actuelle
                                        </button>
                                        <button type="button" id="clearLocationBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Effacer
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="location-help-text">Cliquez sur la carte pour sélectionner votre localisation ou utilisez votre position actuelle</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="client_profile_photo" class="form-label">Photo de profil (optionnelle)</label>
                            <div class="file-input-wrapper">
                                <input id="client_profile_photo" name="client_profile_photo" type="file" class="form-control-file">
                                <div class="file-name-display"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="submit-button">
                        <span class="button-text">S'inscrire en tant que Client</span>
                        <span class="button-loader"></span>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Formulaire Prestataire -->
        <div id="prestataire-form" class="form-section">
            <form id="prestataire-form-element" class="mt-8 space-y-6" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_type" value="prestataire">
                
                <!-- Section: Informations de connexion -->
                <div class="mb-8">
                    <h3 class="section-header">Informations de connexion</h3>
                    
                    <div class="space-y-4">
                        <div class="form-group">
                            <label for="prestataire_name" class="form-label">Identifiant</label>
                            <input id="prestataire_name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" class="form-control" placeholder="Votre identifiant">
                        </div>
                        
                        <div class="form-group">
                            <label for="prestataire_email" class="form-label">E-mail</label>
                            <input id="prestataire_email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="form-control" placeholder="votre@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="prestataire_password" class="form-label">Mot de passe</label>
                            <input id="prestataire_password" name="password" type="password" autocomplete="new-password" required class="form-control" placeholder="Minimum 8 caractères">
                        </div>
                        
                        <div class="form-group">
                            <label for="prestataire_password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input id="prestataire_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="form-control" placeholder="Confirmez votre mot de passe">
                        </div>
                    </div>
                </div>
                
                <!-- Section: Informations professionnelles -->
                <div>
                    <h3 class="section-header">Informations professionnelles</h3>
                    
                    <div class="space-y-4">
                        <div class="form-group">
                            <label for="company_name" class="form-label">Nom de l'enseigne</label>
                            <input id="company_name" name="company_name" type="text" required value="{{ old('company_name') }}" class="form-control" placeholder="Nom de votre entreprise">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" class="form-control" placeholder="Votre numéro de téléphone">
                        </div>
                        
                        <div class="form-group">
                            <label for="prestataire_location" class="form-label">Localisation *</label>
                            <div class="map-container">
                                <div id="prestataireRegistrationMap" class="h-64 rounded-lg border border-gray-300"></div>
                                <div class="mt-3">
                                    <input type="text" id="prestataireSelectedAddress" name="city" value="{{ old('city') }}" class="form-control" placeholder="Cliquez sur la carte pour sélectionner votre localisation" readonly>
                                    <input type="hidden" id="prestataireSelectedLatitude" name="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" id="prestataireSelectedLongitude" name="longitude" value="{{ old('longitude') }}">
                                    <div class="flex gap-3 mt-3">
                                        <button type="button" id="prestataireGetCurrentLocationBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Ma position actuelle
                                        </button>
                                        <button type="button" id="prestataireClearLocationBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Effacer
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="location-help-text">Cliquez sur la carte pour sélectionner votre localisation ou utilisez votre position actuelle</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="prestataire_profile_photo" class="form-label">Photo de profil</label>
                            <div class="file-input-wrapper">
                                <input id="prestataire_profile_photo" name="prestataire_profile_photo" type="file" required class="form-control-file">
                                <div class="file-name-display"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id" class="form-label">Catégorie de service *</label>
                            <select id="category_id" name="category_id" required class="form-control">
                                <option value="">Sélectionnez une catégorie</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="subcategory-group" style="display: none;">
                            <label for="subcategory_id" class="form-label">Sous-catégorie *</label>
                            <select id="subcategory_id" name="subcategory_id" class="form-control">
                                <option value="">Sélectionnez une sous-catégorie</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description" class="form-label">Description courte du service</label>
                            <textarea id="description" name="description" rows="3" class="form-control" placeholder="Décrivez brièvement vos services...">{{ old('description') }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="portfolio_url" class="form-label">Lien vers un portfolio ou site (optionnel)</label>
                            <input id="portfolio_url" name="portfolio_url" type="url" value="{{ old('portfolio_url') }}" class="form-control" placeholder="https://votre-site.com">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="submit-button">
                        <span class="button-text">S'inscrire en tant que Prestataire</span>
                        <span class="button-loader"></span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="login-link">
            Vous avez déjà un compte? <a href="/login">Connectez-vous ici</a>
        </div>

<script src="{{ asset('js/register-form.js') }}"></script>

<script>
// Variables globales
let clientRegistrationMap = null;
let prestataireRegistrationMap = null;
let currentMarker = null;
let prestataireCurrentMarker = null;

// Gestion du sélecteur de type d'utilisateur
document.addEventListener('DOMContentLoaded', function() {
    const userTypeOptions = document.querySelectorAll('.user-type-option');
    const clientForm = document.getElementById('client-form');
    const prestataireForm = document.getElementById('prestataire-form');
    const reassuranceText = document.getElementById('reassurance-text');
    
    // Function to disable form fields (so they won't be validated or submitted)
    function disableFormFields(form) {
        if (form) {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                // Disable ALL inputs including hidden ones (except CSRF token)
                if (input.name !== '_token') {
                    input.disabled = true;
                    input.removeAttribute('required');
                }
            });
        }
    }
    
    // Function to enable form fields
    function enableFormFields(form) {
        if (form) {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                // Enable ALL inputs in the active form (except CSRF token)
                if (input.name !== '_token') {
                    input.disabled = false;
                    
                    // Add required attribute back to required fields based on form type
                    if (form.id === 'client-form-element') {
                        // Client required fields - only basic fields are required for clients
                        if (['client_name', 'client_email', 'client_password', 'client_password_confirmation'].includes(input.id)) {
                            input.setAttribute('required', 'required');
                        }
                    } else if (form.id === 'prestataire-form-element') {
                        // Prestataire required fields - based on the actual form structure
                        const requiredFields = [
                            'prestataire_name', 'prestataire_email', 'prestataire_password', 
                            'prestataire_password_confirmation', 'company_name', 'phone', 
                            'category_id', 'prestataire_profile_photo'
                        ];
                        
                        if (requiredFields.includes(input.id)) {
                            input.setAttribute('required', 'required');
                        }
                        
                        // Special case for city field which has a different ID
                        if (input.id === 'prestataireSelectedAddress') {
                            input.setAttribute('required', 'required');
                        }
                    }
                }
            });
        }
    }
    
    userTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Retirer la sélection de toutes les options
            userTypeOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Ajouter la sélection à l'option cliquée
            this.classList.add('selected');
            
            const userType = this.dataset.type;
            
            // Masquer tous les formulaires et désactiver leurs champs
            clientForm.classList.remove('active');
            prestataireForm.classList.remove('active');
            disableFormFields(clientForm);
            disableFormFields(prestataireForm);
            
            // Afficher le formulaire approprié et activer ses champs
            if (userType === 'client') {
                clientForm.classList.add('active');
                clientForm.style.display = 'block';
                enableFormFields(clientForm);
                // Ensure the client form has the correct user_type value AFTER enabling fields
                setTimeout(() => {
                    const clientUserTypeInput = clientForm.querySelector('input[name="user_type"]');
                    if (clientUserTypeInput) {
                        clientUserTypeInput.disabled = false;
                        clientUserTypeInput.value = 'client';
                        console.log('Set client user_type to:', clientUserTypeInput.value);
                    }
                }, 10);
                // Completely remove the prestataire form from DOM to prevent any submission
                prestataireForm.style.display = 'none';
                prestataireForm.remove();
                reassuranceText.textContent = '100% gratuit – Trouvez des professionnels qualifiés en quelques clics.';
                // Initialiser la carte après un délai pour s'assurer que l'élément est visible
                setTimeout(() => {
                    if (!clientRegistrationMap) {
                        initializeClientRegistrationMap();
                    }
                }, 100);
            } else if (userType === 'prestataire') {
                prestataireForm.classList.add('active');
                prestataireForm.style.display = 'block';
                enableFormFields(prestataireForm);
                // Ensure the prestataire form has the correct user_type value AFTER enabling fields
                setTimeout(() => {
                    const prestataireUserTypeInput = prestataireForm.querySelector('input[name="user_type"]');
                    if (prestataireUserTypeInput) {
                        prestataireUserTypeInput.disabled = false;
                        prestataireUserTypeInput.value = 'prestataire';
                        console.log('Set prestataire user_type to:', prestataireUserTypeInput.value);
                    }
                }, 10);
                // Completely remove the client form from DOM to prevent any submission
                clientForm.style.display = 'none';
                clientForm.remove();
                reassuranceText.textContent = '100% gratuit – Proposez vos services professionnels en quelques clics.';
                // Initialiser la carte après un délai pour s'assurer que l'élément est visible
                setTimeout(() => {
                    if (!prestataireRegistrationMap) {
                        initializePrestataireRegistrationMap();
                    }
                }, 100);
            }
        });
    });
    
    // Initialiser l'état par défaut (aucun formulaire sélectionné)
    disableFormFields(clientForm);
    disableFormFields(prestataireForm);
    
    // Ajouter la validation avant soumission pour s'assurer qu'un type est sélectionné
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const selectedOption = document.querySelector('.user-type-option.selected');
            const submitButton = form.querySelector('button[type="submit"]');
            
            if (!selectedOption) {
                e.preventDefault();
                alert('Veuillez sélectionner un type de compte (Client ou Prestataire) avant de continuer.');
                return false;
            }
            
            // Only allow the active form to be submitted
            const selectedType = selectedOption.dataset.type;
            const currentFormType = form.id === 'client-form-element' ? 'client' : 'prestataire';
            
            console.log('Form submission attempt:', {
                selectedType: selectedType,
                currentFormType: currentFormType,
                formId: form.id,
                shouldSubmit: selectedType === currentFormType
            });
            
            if (selectedType !== currentFormType) {
                console.log('Preventing form submission - wrong form type');
                e.preventDefault();
                return false;
            }
            
            console.log('Allowing form submission');
            
            // Debug: Log all form data before submission
            const formData = new FormData(form);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }
            
            // Show loading state for the correct form
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Inscription en cours...';
                
                // Fallback to re-enable button after 10 seconds
                setTimeout(() => {
                    submitButton.disabled = false;
                    if (form.id === 'client-form-element') {
                        submitButton.innerHTML = 'S\'inscrire en tant que Client';
                    } else {
                        submitButton.innerHTML = 'S\'inscrire en tant que Prestataire';
                    }
                }, 10000);
            }
            
            // Show loading state for the correct form
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Inscription en cours...';
                
                // Fallback to re-enable button after 10 seconds
                setTimeout(() => {
                    submitButton.disabled = false;
                    if (form.id === 'client-form-element') {
                        submitButton.innerHTML = 'S\'inscrire en tant que Client';
                    } else {
                        submitButton.innerHTML = 'S\'inscrire en tant que Prestataire';
                    }
                }, 10000);
            }
            
            // Allow form to submit normally
            return true;
        });
    });
    
    // Gestion des fichiers
    const fileInputs = document.querySelectorAll('.form-control-file');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : '';
            const display = this.parentElement.querySelector('.file-name-display');
            display.textContent = fileName;
        });
    });
    
    // Gestion des catégories pour prestataires
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    const subcategoryGroup = document.getElementById('subcategory-group');
    
    // Charger les catégories principales
    if (categorySelect) {
        loadMainCategories();
        
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            if (categoryId) {
                loadSubcategories(categoryId);
            } else {
                subcategoryGroup.style.display = 'none';
                subcategorySelect.innerHTML = '<option value="">Sélectionnez une sous-catégorie</option>';
            }
        });
    }
    
    // Gestion des boutons de géolocalisation pour prestataires
    const prestataireGetCurrentLocationBtn = document.getElementById('prestataireGetCurrentLocationBtn');
    const prestataireClearLocationBtn = document.getElementById('prestataireClearLocationBtn');
    
    if (prestataireGetCurrentLocationBtn) {
        prestataireGetCurrentLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        if (prestataireRegistrationMap) {
                            prestataireRegistrationMap.setView([lat, lng], 15);
                            
                            if (prestataireCurrentMarker) {
                                prestataireRegistrationMap.removeLayer(prestataireCurrentMarker);
                            }
                            
                            prestataireCurrentMarker = L.marker([lat, lng]).addTo(prestataireRegistrationMap);
                            
                            // Géocodage inverse
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=fr`)
                                .then(response => response.json())
                                .then(data => {
                                    const address = data.display_name || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                                    document.getElementById('prestataireSelectedAddress').value = address;
                                    document.getElementById('prestataireSelectedLatitude').value = lat;
                                    document.getElementById('prestataireSelectedLongitude').value = lng;
                                })
                                .catch(() => {
                                    document.getElementById('prestataireSelectedAddress').value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                                    document.getElementById('prestataireSelectedLatitude').value = lat;
                                    document.getElementById('prestataireSelectedLongitude').value = lng;
                                });
                        }
                    },
                    function() {
                        alert('Impossible de détecter votre position');
                    }
                );
            } else {
                alert('La géolocalisation n\'est pas supportée par ce navigateur');
            }
        });
    }
    
    if (prestataireClearLocationBtn) {
        prestataireClearLocationBtn.addEventListener('click', function() {
            if (prestataireCurrentMarker && prestataireRegistrationMap) {
                prestataireRegistrationMap.removeLayer(prestataireCurrentMarker);
                prestataireCurrentMarker = null;
            }
            document.getElementById('prestataireSelectedAddress').value = '';
            document.getElementById('prestataireSelectedLatitude').value = '';
            document.getElementById('prestataireSelectedLongitude').value = '';
        });
    }
});

// Fonctions pour la carte Leaflet (clients)
function initializeClientRegistrationMap() {
    const mapElement = document.getElementById('clientRegistrationMap');
    if (!mapElement) return;

    const defaultLat = 33.5731;
    const defaultLng = -7.5898;

    clientRegistrationMap = L.map('clientRegistrationMap', {
        center: [defaultLat, defaultLng],
        zoom: 6
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(clientRegistrationMap);

    clientRegistrationMap.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (currentMarker) clientRegistrationMap.removeLayer(currentMarker);

        currentMarker = L.marker([lat, lng]).addTo(clientRegistrationMap);

        document.getElementById('selectedLatitude').value = lat.toFixed(6);
        document.getElementById('selectedLongitude').value = lng.toFixed(6);

        reverseGeocodeClient(lat, lng);
    });

    setTimeout(() => clientRegistrationMap.invalidateSize(), 250);
}

async function reverseGeocodeClient(lat, lng) {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=fr`);
        const data = await response.json();
        document.getElementById('selectedAddress').value = data.display_name || `Coordonnées: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    } catch (error) {
        document.getElementById('selectedAddress').value = `Coordonnées: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
}

function clearClientLocation() {
    if (currentMarker) clientRegistrationMap.removeLayer(currentMarker);
    currentMarker = null;
    document.getElementById('selectedLatitude').value = '';
    document.getElementById('selectedLongitude').value = '';
    document.getElementById('selectedAddress').value = '';
}

function getCurrentClientLocation() {
    if (!navigator.geolocation) {
        alert('La géolocalisation n\'est pas supportée par votre navigateur.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        position => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            clientRegistrationMap.setView([lat, lng], 15);
            if (currentMarker) clientRegistrationMap.removeLayer(currentMarker);
            currentMarker = L.marker([lat, lng]).addTo(clientRegistrationMap);

            document.getElementById('selectedLatitude').value = lat.toFixed(6);
            document.getElementById('selectedLongitude').value = lng.toFixed(6);

            reverseGeocodeClient(lat, lng);
        },
        error => {
            let errorMessage = 'Erreur de géolocalisation.';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'Accès à la géolocalisation refusé. Veuillez autoriser l\'accès à votre position.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Position non disponible.';
                    break;
                case error.TIMEOUT:
                    errorMessage = 'Délai d\'attente dépassé.';
                    break;
            }
            alert(errorMessage);
        }
    );
}

// Événements pour les boutons de la carte client
document.addEventListener('DOMContentLoaded', () => {
    const getCurrentLocationBtn = document.getElementById('getCurrentLocationBtn');
    const clearLocationBtn = document.getElementById('clearLocationBtn');
    
    if (getCurrentLocationBtn) {
        getCurrentLocationBtn.addEventListener('click', getCurrentClientLocation);
    }
    
    if (clearLocationBtn) {
        clearLocationBtn.addEventListener('click', clearClientLocation);
    }
});

// Fonction pour initialiser la carte des prestataires
function initializePrestataireRegistrationMap() {
    const mapElement = document.getElementById('prestataireRegistrationMap');
    if (!mapElement) return;

    const defaultLat = 33.5731;
    const defaultLng = -7.5898;

    prestataireRegistrationMap = L.map('prestataireRegistrationMap', {
        center: [defaultLat, defaultLng],
        zoom: 6
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(prestataireRegistrationMap);

    prestataireRegistrationMap.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (prestataireCurrentMarker) {
            prestataireRegistrationMap.removeLayer(prestataireCurrentMarker);
        }

        prestataireCurrentMarker = L.marker([lat, lng]).addTo(prestataireRegistrationMap);

        // Géocodage inverse pour obtenir l'adresse
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=fr`)
            .then(response => response.json())
            .then(data => {
                const address = data.display_name || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                document.getElementById('prestataireSelectedAddress').value = address;
                document.getElementById('prestataireSelectedLatitude').value = lat;
                document.getElementById('prestataireSelectedLongitude').value = lng;
            })
            .catch(error => {
                console.error('Erreur lors du géocodage inverse:', error);
                document.getElementById('prestataireSelectedAddress').value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                document.getElementById('prestataireSelectedLatitude').value = lat;
                document.getElementById('prestataireSelectedLongitude').value = lng;
            });
    });
}

// Fonctions pour la gestion des catégories
function loadMainCategories() {
    fetch('/categories/main')
        .then(response => response.json())
        .then(categories => {
            const categorySelect = document.getElementById('category_id');
            categorySelect.innerHTML = '<option value="">Sélectionnez une catégorie</option>';
            
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des catégories:', error);
        });
}

function loadSubcategories(categoryId) {
    fetch(`/api/categories/${categoryId}/subcategories`)
        .then(response => response.json())
        .then(subcategories => {
            const subcategorySelect = document.getElementById('subcategory_id');
            const subcategoryGroup = document.getElementById('subcategory-group');
            
            subcategorySelect.innerHTML = '<option value="">Sélectionnez une sous-catégorie</option>';
            
            if (subcategories.length > 0) {
                subcategories.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    subcategorySelect.appendChild(option);
                });
                subcategoryGroup.style.display = 'block';
            } else {
                subcategoryGroup.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des sous-catégories:', error);
            const subcategoryGroup = document.getElementById('subcategory-group');
            subcategoryGroup.style.display = 'none';
        });
}

// CSRF Token Refresh Function
function refreshCSRFToken() {
    fetch('/csrf-token')
        .then(response => response.json())
        .then(data => {
            // Update all CSRF tokens in forms
            document.querySelectorAll('input[name="_token"]').forEach(input => {
                input.value = data.csrf_token;
            });
            // Update meta tag if exists
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', data.csrf_token);
            }
        })
        .catch(error => {
            console.error('Error refreshing CSRF token:', error);
        });
}

// Refresh CSRF token every 30 minutes
setInterval(refreshCSRFToken, 30 * 60 * 1000);

// Refresh CSRF token when page becomes visible (user returns to tab)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        refreshCSRFToken();
    }
});

// Store original button text and reset loading state on page load
document.querySelectorAll('button[type="submit"]').forEach(button => {
    button.setAttribute('data-original-text', button.innerHTML);
    // Reset button state on page load (in case of validation errors)
    button.disabled = false;
    button.innerHTML = button.getAttribute('data-original-text');
});

</script>

    </div>
</div>
@endsection

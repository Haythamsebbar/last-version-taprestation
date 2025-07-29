@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register-form.css') }}">
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

.reassurance-banner {
    padding: 1rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    text-align: center;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    font-weight: 500;
}

.reassurance-banner i {
    margin-right: 0.5rem;
}
</style>
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
            <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="client_location" class="form-label">Ville</label>
                            <div class="location-input-wrapper">
                                <input id="client_location" name="location" type="text" value="{{ old('location') }}" class="form-control" placeholder="Votre ville...">
                                <button type="button" class="location-button use-location-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="location-help-text">Cliquez sur l'icône pour utiliser votre position actuelle</p>
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
            <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="city" class="form-label">Ville</label>
                            <div class="location-input-wrapper">
                                <input id="city" name="city" type="text" required value="{{ old('city') }}" class="form-control" placeholder="Rechercher une ville...">
                                <button type="button" class="location-button use-location-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="location-help-text">Cliquez sur l'icône pour utiliser votre position actuelle</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="prestataire_profile_photo" class="form-label">Photo de profil</label>
                            <div class="file-input-wrapper">
                                <input id="prestataire_profile_photo" name="prestataire_profile_photo" type="file" required class="form-control-file">
                                <div class="file-name-display"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id" class="form-label">Catégorie principale</label>
                            <select id="category_id" name="category_id" required class="form-control">
                                <option value="">Sélectionnez une catégorie</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="subcategory_id" class="form-label">Sous-catégorie</label>
                            <select id="subcategory_id" name="subcategory_id" class="form-control" disabled>
                                <option value="">Sélectionnez d'abord une catégorie</option>
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
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeOptions = document.querySelectorAll('.user-type-option');
    const clientForm = document.getElementById('client-form');
    const prestataireForm = document.getElementById('prestataire-form');
    const reassuranceText = document.getElementById('reassurance-text');
    
    userTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Retirer la sélection de toutes les options
            userTypeOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Ajouter la sélection à l'option cliquée
            this.classList.add('selected');
            
            const userType = this.dataset.type;
            
            // Masquer tous les formulaires
            clientForm.classList.remove('active');
            prestataireForm.classList.remove('active');
            
            // Afficher le formulaire approprié
            if (userType === 'client') {
                clientForm.classList.add('active');
                reassuranceText.textContent = '100% gratuit – Trouvez des professionnels qualifiés en quelques clics.';
            } else if (userType === 'prestataire') {
                prestataireForm.classList.add('active');
                reassuranceText.textContent = '10% gratuit – Proposez vos services professionnels en quelques clics.';
            }
        });
    });
    
    // Gestion de la géolocalisation
    const useLocationBtns = document.querySelectorAll('.use-location-btn');
    
    useLocationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Ici vous pouvez utiliser une API de géocodage inverse
                    // Pour l'exemple, on met juste un placeholder
                    const locationInput = btn.parentElement.querySelector('input');
                    locationInput.value = 'Position détectée';
                }, function() {
                    alert('Impossible de détecter votre position');
                });
            } else {
                alert('La géolocalisation n\'est pas supportée par ce navigateur');
            }
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
    
    // Gestion des catégories et sous-catégories
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    
    // Charger les catégories principales au chargement de la page
    if (categorySelect) {
        loadMainCategories();
        
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            if (categoryId) {
                loadSubcategories(categoryId);
                subcategorySelect.disabled = false;
            } else {
                subcategorySelect.innerHTML = '<option value="">Sélectionnez d\'abord une catégorie</option>';
                subcategorySelect.disabled = true;
            }
        });
    }
    
    function loadMainCategories() {
        fetch('/categories/main')
            .then(response => response.json())
            .then(categories => {
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
                 subcategorySelect.innerHTML = '<option value="">Sélectionnez une sous-catégorie (optionnel)</option>';
                 subcategories.forEach(subcategory => {
                     const option = document.createElement('option');
                     option.value = subcategory.id;
                     option.textContent = subcategory.name;
                     subcategorySelect.appendChild(option);
                 });
             })
             .catch(error => {
                 console.error('Erreur lors du chargement des sous-catégories:', error);
                 subcategorySelect.innerHTML = '<option value="">Erreur de chargement</option>';
             });
     }
});
</script>
@endsection
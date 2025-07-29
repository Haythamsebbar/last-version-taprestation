@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register-form.css') }}">
@endpush

@section('content')
<div class="register-container">
    <div class="reassurance-banner">
        <i class="fas fa-check-circle"></i> 100% gratuit – Trouvez ou proposez vos services en quelques clics.
    </div>
    <div class="register-card prestataire-form">
        <div>
            <h2 class="register-title">
                Créer votre compte prestataire
            </h2>
            <p class="register-subtitle">
                Rejoignez TaPrestation pour offrir vos services professionnels
            </p>
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
        
        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_type" value="prestataire">
            
            <!-- Section: Informations de connexion -->
            <div class="mb-8">
                <h3 class="section-header">Informations de connexion</h3>
                
                <div class="space-y-4">
                    <div class="form-group">
                        <label for="name" class="form-label">Identifiant</label>
                        <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" class="form-control" placeholder="Votre identifiant">
                    </div>
                    
                    <div class="form-group">
                        <label for="email-address" class="form-label">E-mail</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="form-control" placeholder="votre@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="form-control" placeholder="Minimum 8 caractères">
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="form-control" placeholder="Confirmez votre mot de passe">
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
                    
                    {{-- <div class="form-group">
                        <label for="category_id" class="form-label">Catégorie</label>
                        <select id="category_id" name="category_id" required class="form-control">
                            <option value="">Sélectionnez une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="subcategory_id" class="form-label">Sous-catégorie</label>
                        <select id="subcategory_id" name="subcategory_id" required class="form-control">
                            <option value="">Sélectionnez d'abord une catégorie</option>
                        </select>
                    </div> --}}
                    
                    <div class="form-group">
                        <label for="city" class="form-label">Ville</label>
                        <div class="location-input-wrapper">
                            <input id="city" name="city" type="text" required value="{{ old('city') }}" class="form-control" placeholder="Rechercher une ville...">
                            <button type="button" id="use_location" class="location-button">
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
                    <span class="button-text">S'inscrire</span>
                    <span class="button-loader"></span>
                </button>
            </div>
        </form>
        <div class="login-link">
            Vous avez déjà un compte? <a href="/login">Connectez-vous ici</a>
        </div>

<script src="{{ asset('js/register-form.js') }}"></script>
    </div>
</div>
@endsection

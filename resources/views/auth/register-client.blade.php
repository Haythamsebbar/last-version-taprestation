@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register-form.css') }}">
@endpush

@section('content')
<div class="register-container">
    <div class="reassurance-banner">
        <i class="fas fa-check-circle"></i> 100% gratuit – Trouvez ou proposez vos services en quelques clics.
    </div>
    <div class="register-card client-form">
        <div>
            <h2 class="register-title">
                Créer votre compte client
            </h2>
            <p class="register-subtitle">
                Rejoignez TaPrestation pour trouver des professionnels qualifiés
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
            <input type="hidden" name="user_type" value="client">
            
            <!-- Section: Informations de connexion -->
            <div class="mb-8">
                <h3 class="section-header">Informations de connexion</h3>
                
                <div class="space-y-4">
                    <div class="form-group">
                        <label for="name" class="form-label">Nom complet</label>
                        <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" class="form-control" placeholder="Votre nom complet">
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
            
            <!-- Section: Informations personnelles -->
            <div>
                <h3 class="section-header">Informations personnelles</h3>
                
                <div class="space-y-4">
                    <div class="form-group">
                        <label for="location" class="form-label">Ville</label>
                        <div class="location-input-wrapper">
                            <input id="location" name="location" type="text" value="{{ old('location') }}" class="form-control" placeholder="Votre ville...">
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
                    <span class="button-text">S'inscrire</span>
                    <span class="button-loader"></span>
                </button>
            </div>
        </form>
        <div class="login-link">
            Vous avez déjà un compte ? <a href="/login">Connectez-vous ici</a>
        </div>

<script src="{{ asset('js/register-form.js') }}"></script>
    </div>
</div>
@endsection
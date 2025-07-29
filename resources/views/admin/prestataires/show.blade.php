@extends('layouts.admin-modern')

@section('title', 'Détails du prestataire')

@push('styles')
<link href="{{ asset('css/admin-prestataires.css') }}" rel="stylesheet">
<style>
    .section-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }
    .section-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 1rem 1.5rem;
        margin: 0;
    }
    .section-header h5 {
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    .profile-avatar:hover {
        transform: scale(1.05);
    }
    .status-badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .verification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        border: 3px solid white;
    }
    .info-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f3f4;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    .info-value {
        color: #6c757d;
    }
    .document-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }
    .document-item:hover {
        background: #e9ecef;
        transform: translateY(-1px);
    }
    .action-btn {
        transition: all 0.2s ease;
        border-radius: 8px;
        font-weight: 500;
    }
    .action-btn:hover {
        transform: translateY(-1px);
    }
    .skill-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        margin: 0.2rem;
        display: inline-block;
    }
    .stat-item {
        text-align: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        margin: 0.5rem;
    }
    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #495057;
    }
    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    @media (max-width: 768px) {
        .section-card {
            margin-bottom: 1rem;
        }
        .profile-section {
            text-align: center;
        }
        .action-buttons {
            flex-direction: column;
            gap: 0.5rem;
        }
        .action-buttons .btn {
            width: 100%;
        }
    }
    
    .d-none {
        display: none !important;
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
    
    /* Fix pour les conflits Bootstrap */
    .admin-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }
    
    .admin-card .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        border-radius: 12px 12px 0 0;
    }
    
    .admin-card .card-body {
        padding: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid prestataire-view py-4" data-current-user-id="{{ auth()->id() }}">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.prestataires.index') }}">Prestataires</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $prestataire->user->name }}</li>
        </ol>
    </nav>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Section Identité -->
    <div class="section-card">
        <div class="section-header">
            <h5><i class="fas fa-user-circle"></i> Identité du Prestataire</h5>
        </div>
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <div class="position-relative d-inline-block">
                        @if($prestataire->photo)
                            <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="Photo de {{ $prestataire->user->name }}" class="profile-avatar">
                        @elseif($prestataire->user->avatar)
                            <img src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="Photo de {{ $prestataire->user->name }}" class="profile-avatar">
                        @elseif($prestataire->user->profile_photo_url)
                            <img src="{{ $prestataire->user->profile_photo_url }}" alt="Photo de {{ $prestataire->user->name }}" class="profile-avatar">
                        @else
                            <div class="profile-avatar bg-gradient-primary text-white d-flex align-items-center justify-content-center">
                                <span style="font-size: 2rem; font-weight: 600;">{{ substr($prestataire->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        @if($prestataire->isVerified())
                            <div class="verification-badge bg-success text-white">
                                <i class="fas fa-check"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h3 class="mb-0 fw-bold">{{ $prestataire->user->name }}</h3>
                        @if($prestataire->isVerified())
                            <span class="badge bg-success d-flex align-items-center gap-1">
                                <i class="fas fa-check"></i> Vérifié
                            </span>
                        @endif
                    </div>
                    <p class="text-muted mb-3"><i class="fas fa-envelope me-2"></i>{{ $prestataire->user->email }}</p>
                    
                    <!-- Statut -->
                    <div class="mb-3">
                        @if($prestataire->user->blocked_at)
                            <span class="status-badge bg-danger text-white">
                                <i class="fas fa-lock"></i> Bloqué
                            </span>
                        @elseif($prestataire->is_approved)
                            <span class="status-badge bg-success text-white">
                                <i class="fas fa-check-circle"></i> Approuvé
                            </span>
                        @else
                            <span class="status-badge bg-warning text-dark">
                                <i class="fas fa-clock"></i> En attente
                            </span>
                        @endif
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="info-label">Date d'inscription</div>
                            <div class="info-value">{{ $prestataire->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Dernière mise à jour</div>
                            <div class="info-value">{{ $prestataire->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="action-buttons d-flex flex-column gap-2">
                        <a href="{{ route('admin.prestataires.index') }}" class="btn btn-secondary btn-sm action-btn">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <a href="{{ route('prestataires.show', $prestataire->id) }}" target="_blank" class="btn btn-outline-primary btn-sm action-btn">
                            <i class="fas fa-eye"></i> Profil public
                        </a>
                        @if(!$prestataire->is_approved)
                            <form method="POST" action="{{ route('admin.prestataires.approve', $prestataire) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm action-btn w-100">
                                    <i class="fas fa-check"></i> Approuver
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.prestataires.toggle-block', $prestataire->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm action-btn w-100 {{ $prestataire->user->blocked_at ? 'btn-success' : 'btn-warning' }}">
                                <i class="fas {{ $prestataire->user->blocked_at ? 'fa-unlock' : 'fa-lock' }}"></i> 
                                {{ $prestataire->user->blocked_at ? 'Débloquer' : 'Bloquer' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        
        <div class="col-lg-6">
            <!-- Section Activité professionnelle -->
            <div class="section-card">
                <div class="section-header">
                    <h5><i class="fas fa-briefcase"></i> Activité Professionnelle</h5>
                </div>
                <div class="card-body p-4">
                    <div class="info-row">
                        <div class="info-label">Secteur d'activité</div>
                        <div class="info-value">{{ $prestataire->secteur_activite ?? 'Non renseigné' }}</div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Compétences</div>
                        <div class="info-value">
                            @if($prestataire->competences)
                                @foreach(explode(',', $prestataire->competences) as $competence)
                                    <span class="skill-badge">{{ trim($competence) }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Non renseignées</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Zone de service</div>
                        <div class="info-value">{{ $prestataire->service_area ?? 'Non renseignée' }}</div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Tarifs</div>
                        <div class="info-value">{{ $prestataire->rates ?? 'Non renseignés' }}</div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Délai moyen de livraison</div>
                        <div class="info-value">{{ $prestataire->average_delivery_time ?? 'Non renseigné' }}</div>
                    </div>
                    
                    @if($prestataire->years_experience)
                        <div class="info-row">
                            <div class="info-label">Années d'expérience</div>
                            <div class="info-value">{{ $prestataire->years_experience }} ans</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section Présentation -->
    @if($prestataire->description)
    <div class="section-card">
        <div class="section-header">
            <h5><i class="fas fa-file-text"></i> Présentation</h5>
        </div>
        <div class="card-body p-4">
            <div class="bg-light p-3 rounded">
                {{ $prestataire->description }}
            </div>
        </div>
    </div>
    @endif
    
    <div class="row">
        <!-- Section Services -->
        <div class="col-lg-6">
            <div class="section-card">
                <div class="section-header">
                    <h5><i class="fas fa-cogs"></i> Services ({{ $prestataire->services ? $prestataire->services->count() : 0 }})</h5>
                </div>
                <div class="card-body p-4">
                    @if($prestataire->services && $prestataire->services->count() > 0)
                        @foreach($prestataire->services->take(5) as $service)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $service->title }}</h6>
                                        <p class="text-muted small mb-1">{{ Str::limit($service->description, 100) }}</p>
                                        <small class="text-muted">Créé le {{ $service->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge bg-primary">{{ number_format($service->price, 2) }} €</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($prestataire->services->count() > 5)
                            <div class="text-center">
                                <a href="{{ route('admin.services.index', ['prestataire' => $prestataire->id]) }}" class="btn btn-outline-primary action-btn">
                                    <i class="fas fa-list"></i> Voir tous les services
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Aucun service proposé
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Section Avis clients -->
        <div class="col-lg-6">
            <div class="section-card">
                <div class="section-header">
                    <h5><i class="fas fa-star"></i> Avis Clients ({{ $prestataire->reviews ? $prestataire->reviews->count() : 0 }})</h5>
                </div>
                <div class="card-body p-4">
                    @if($prestataire->reviews && $prestataire->reviews->count() > 0)
                        @foreach($prestataire->reviews->take(5) as $review)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h6 class="fw-bold mb-0">{{ $review->client_name }}</h6>
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                                <span class="ms-1 small text-muted">({{ $review->rating }}/5)</span>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-1">{{ Str::limit($review->comment, 120) }}</p>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($prestataire->reviews->count() > 5)
                            <div class="text-center">
                                <a href="{{ route('admin.reviews.index', ['prestataire' => $prestataire->id]) }}" class="btn btn-outline-primary action-btn">
                                    <i class="fas fa-comments"></i> Voir tous les avis
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Aucun avis reçu
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistiques -->
    <div class="section-card">
        <div class="section-header">
            <h5><i class="fas fa-chart-bar"></i> Statistiques</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">{{ $prestataire->services ? $prestataire->services->count() : 0 }}</div>
                        <div class="stat-label">Services</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">{{ $prestataire->reviews ? $prestataire->reviews->count() : 0 }}</div>
                        <div class="stat-label">Avis</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">{{ $prestataire->offers ? $prestataire->offers->count() : 0 }}</div>
                        <div class="stat-label">Offres</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">{{ $prestataire->rating_average ? number_format($prestataire->rating_average, 1) : '0.0' }}</div>
                        <div class="stat-label">Note moyenne</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-user-details.js') }}"></script>
<script src="{{ asset('js/messaging.js') }}"></script>
<script>
    // Initialiser les gestionnaires après le chargement du DOM
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier que les classes sont disponibles
        if (typeof UserDetailsManager !== 'undefined') {
            window.userDetailsManager = new UserDetailsManager();
            console.log('UserDetailsManager initialisé');
        } else {
            console.warn('UserDetailsManager non disponible');
        }
        
        if (typeof MessagingSystem !== 'undefined') {
            window.messagingSystem = new MessagingSystem();
            console.log('MessagingSystem initialisé');
        } else {
            console.warn('MessagingSystem non disponible');
        }
    });
</script>
@endpush
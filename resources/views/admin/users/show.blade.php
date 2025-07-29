@extends('layouts.admin-modern')

@section('title', 'Détails de l\'utilisateur')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/admin-user-details.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Détails de l'utilisateur</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('administrateur.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('administrateur.users.index') }}">Utilisateurs</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <button class="btn btn-outline-secondary btn-sm me-2" id="refreshBtn" data-bs-toggle="tooltip" title="Actualiser">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <a href="{{ route('administrateur.users.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Informations de l'utilisateur</h5>
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <button class="btn btn-{{ $user->is_blocked ? 'warning' : 'danger' }} btn-sm" id="toggleBlockBtn" data-bs-toggle="tooltip" title="{{ $user->is_blocked ? 'Débloquer cet utilisateur' : 'Bloquer cet utilisateur' }}">
                            <i class="bi bi-{{ $user->is_blocked ? 'unlock' : 'lock' }}"></i>
                            {{ $user->is_blocked ? 'Débloquer' : 'Bloquer' }}
                        </button>
                        <button class="btn btn-danger btn-sm" id="deleteUserBtn" data-bs-toggle="tooltip" title="Supprimer définitivement cet utilisateur">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar-circle" data-bs-toggle="tooltip" title="{{ $user->name }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h4 class="mb-1 d-flex align-items-center">
                                {{ $user->name }}
                                @if($user->email_verified_at)
                                    <i class="bi bi-patch-check-fill text-success ms-2" data-bs-toggle="tooltip" title="Email vérifié"></i>
                                @else
                                    <i class="bi bi-patch-exclamation-fill text-warning ms-2" data-bs-toggle="tooltip" title="Email non vérifié"></i>
                                @endif
                            </h4>
                            <p class="text-muted mb-2 d-flex align-items-center">
                                <i class="bi bi-envelope me-2"></i>
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                            </p>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-{{ $user->is_blocked ? 'danger' : 'success' }} fs-6">
                                    <i class="bi bi-{{ $user->is_blocked ? 'x-circle' : 'check-circle' }} me-1"></i>
                                    {{ $user->is_blocked ? 'Bloqué' : 'Actif' }}
                                </span>
                                <span class="badge bg-info fs-6">
                                    <i class="bi bi-person-badge me-1"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="bi bi-calendar-plus text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-1">Date d'inscription</h6>
                                    <p class="mb-0 text-muted">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="bi bi-clock-history text-info me-3"></i>
                                <div>
                                    <h6 class="mb-1">Dernière mise à jour</h6>
                                    <p class="mb-0 text-muted">{{ $user->updated_at->format('d/m/Y à H:i') }}</p>
                                    <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($user->phone)
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="bi bi-telephone text-success me-3"></i>
                                <div>
                                    <h6 class="mb-1">Téléphone</h6>
                                    <p class="mb-0">
                                        <a href="tel:{{ $user->phone }}" class="text-decoration-none">{{ $user->phone }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @if($user->email_verified_at)
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="bi bi-patch-check text-success me-3"></i>
                                <div>
                                    <h6 class="mb-1">Email vérifié le</h6>
                                    <p class="mb-0 text-muted">{{ $user->email_verified_at->format('d/m/Y à H:i') }}</p>
                                    <small class="text-muted">{{ $user->email_verified_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($user->role === 'prestataire' && $user->prestataire)
                        <div class="card mt-4 border-0 shadow-sm">
                            <div class="card-header bg-gradient-info text-white">
                                <h5 class="mb-0 d-flex align-items-center">
                                    <i class="bi bi-briefcase me-2"></i>
                                    Informations Prestataire
                                    @if($user->prestataire->approved_at)
                                        <span class="badge bg-success ms-auto">
                                            <i class="bi bi-patch-check me-1"></i>Vérifié
                                        </span>
                                    @else
                                        <span class="badge bg-warning ms-auto">
                                            <i class="bi bi-clock me-1"></i>En attente
                                        </span>
                                    @endif
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <i class="bi bi-building text-success me-2"></i>
                                            <div>
                                                <h6 class="mb-1 font-weight-bold">Secteur d'activité</h6>
                                                <p class="mb-0 fw-medium">{{ $user->prestataire->sector ?? 'Non défini' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <i class="bi bi-geo-alt text-danger me-2"></i>
                                            <div>
                                                <h6 class="mb-1 font-weight-bold">Localisation</h6>
                                                <p class="mb-0 fw-medium">{{ $user->prestataire->location ?? 'Non définie' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="info-item text-center">
                                            <i class="bi bi-list-task text-primary me-2"></i>
                                            <div>
                                                <h6 class="mb-1">Services</h6>
                                                <p class="mb-0 fs-4 fw-bold text-primary">{{ $user->prestataire->services->count() ?? 0 }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item text-center">
                                            <i class="bi bi-star text-warning me-2"></i>
                                            <div>
                                                <h6 class="mb-1">Note moyenne</h6>
                                                <p class="mb-0 fs-4 fw-bold text-warning">
                                                    {{ number_format($user->prestataire->average_rating ?? 0, 1) }}/5
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('administrateur.prestataires.show', $user->prestataire->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-person-badge"></i> Voir profil prestataire
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($user->role === 'client' && $user->client)
                        <div class="card mt-4 border-0 shadow-sm">
                            <div class="card-header bg-gradient-secondary text-white">
                                <h5 class="mb-0 d-flex align-items-center">
                                    <i class="bi bi-person-heart me-2"></i>
                                    Informations Client
                                    <span class="badge bg-light text-dark ms-auto">
                                        <i class="bi bi-calendar me-1"></i>
                                        Membre depuis {{ $user->created_at->format('M Y') }}
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="info-item text-center">
                                            <i class="bi bi-clipboard-check text-primary me-2"></i>
                                            <div>
                                                <h6 class="mb-1">Demandes</h6>
                                                <p class="mb-0 fs-4 fw-bold text-primary">{{ $user->client->clientRequests->count() ?? 0 }}</p>
                                                <small class="text-muted">Total créées</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item text-center">
                                            <i class="bi bi-star text-warning me-2"></i>
                                            <div>
                                                <h6 class="mb-1">Avis</h6>
                                                <p class="mb-0 fs-4 fw-bold text-warning">{{ $user->client->reviews->count() ?? 0 }}</p>
                                                <small class="text-muted">Avis laissés</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-item text-center">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            <div>
                                                <h6 class="mb-1">Complétées</h6>
                                                <p class="mb-0 fs-4 fw-bold text-success">{{ $user->client->completed_requests ?? 0 }}</p>
                                                <small class="text-muted">Demandes finalisées</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 d-flex gap-2 flex-wrap">
                                    @if($user->client)
                                    <a href="{{ route('administrateur.clients.show', $user->client->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Voir le profil complet
                                    </a>
                                    @endif
                                    {{-- TODO: Add admin routes for client requests and reviews --}}
                                    {{-- @if($user->client)
                                    <a href="{{ route('administrateur.requests.index', ['client_id' => $user->client->id]) }}" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-list"></i> Voir les demandes
                                    </a>
                                    @endif --}}
                                    {{-- @if($user->client && $user->client->reviews && $user->client->reviews->count() > 0)
                                    <a href="{{ route('administrateur.reviews.index', ['client_id' => $user->client->id]) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-star"></i> Voir les avis
                                    </a>
                                    @endif --}}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Stats Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Statistiques rapides</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-calendar-plus text-primary fs-4"></i>
                                <div class="mt-2">
                                    <div class="fw-bold">{{ $user->created_at->diffInDays() }}</div>
                                    <small class="text-muted">Jours d'ancienneté</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-clock text-info fs-4"></i>
                                <div class="mt-2">
                                    <div class="fw-bold">{{ $user->last_login_at ? $user->last_login_at->diffInDays() : 'N/A' }}</div>
                                    <small class="text-muted">Jours depuis connexion</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($user->role === 'prestataire' && $user->prestataire)
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                                <div class="mt-2">
                                    <div class="fw-bold text-success">{{ $user->prestataire->services->where('is_active', true)->count() }}</div>
                                    <small class="text-muted">Services actifs</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-star-fill text-warning fs-4"></i>
                                <div class="mt-2">
                                    <div class="fw-bold text-warning">{{ number_format($user->prestataire->average_rating ?? 0, 1) }}</div>
                                    <small class="text-muted">Note moyenne</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Activity Timeline Card -->
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-gradient-secondary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="bi bi-activity me-2"></i>Activité récente</h6>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <div class="activity-item">
                            <div class="activity-icon bg-primary">
                                <i class="bi bi-clock text-white"></i>
                            </div>
                            <div class="activity-content">
                                <h6 class="mb-1">Dernière connexion</h6>
                                <p class="mb-0 text-muted">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('d/m/Y à H:i') }}
                                        <br><small class="text-primary">{{ $user->last_login_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-warning">Jamais connecté</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($user->role === 'prestataire' && $user->prestataire)
                            <div class="activity-item">
                                <div class="activity-icon bg-success">
                                    <i class="bi bi-briefcase text-white"></i>
                                </div>
                                <div class="activity-content">
                                    <h6 class="mb-1">Services proposés</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $user->prestataire->services()->count() }} service(s) au total
                                        <br><small class="text-success">{{ $user->prestataire->services->where('is_active', true)->count() }} actif(s)</small>
                                    </p>
                                </div>
                            </div>
                            
                            @if($user->prestataire->services->count() > 0)
                            <div class="activity-item">
                                <div class="activity-icon bg-info">
                                    <i class="bi bi-calendar-check text-white"></i>
                                </div>
                                <div class="activity-content">
                                    <h6 class="mb-1">Dernière mission</h6>
                                    <p class="mb-0 text-muted">
                                        @if($user->prestataire->last_mission_at)
                                            {{ $user->prestataire->last_mission_at->diffForHumans() }}
                                        @else
                                            Aucune mission encore
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif
                        @endif
                        @if($user->role === 'client' && $user->client)
                            <div class="activity-item">
                                <div class="activity-icon bg-warning">
                                    <i class="bi bi-clipboard-check text-white"></i>
                                </div>
                                <div class="activity-content">
                                    <h6 class="mb-1">Demandes créées</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $user->client->clientRequests->count() }} demande(s) au total
                                        <br><small class="text-info">{{ $user->client->clientRequests->where('status', 'active')->count() }} en cours</small>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon bg-warning">
                                    <i class="bi bi-star text-white"></i>
                                </div>
                                <div class="activity-content">
                                    <h6 class="mb-1">Avis et évaluations</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $user->client->reviews()->count() }} avis laissé(s)
                                        @if($user->client->reviews->count() > 0)
                                            <br><small class="text-warning">Note moyenne donnée: {{ number_format($user->client->reviews->avg('rating'), 1) }}/5</small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@section('scripts')
    <script src="{{ asset('js/admin-user-details.js') }}"></script>
@endsection
@extends('layouts.app')

@section('title', 'Détails de l\'Alerte de Correspondance')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('matching-alerts.index') }}">Mes Alertes</a></li>
                            <li class="breadcrumb-item active">Détails de l'alerte</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">Alerte de Correspondance</h1>
                </div>
                <div class="d-flex gap-2">
                    @if(!$alert->is_read)
                        <button class="btn btn-success" onclick="markAsRead({{ $alert->id }})">
                            <i class="fas fa-check"></i> Marquer comme lu
                        </button>
                    @endif
                    @if(!$alert->is_dismissed)
                        <button class="btn btn-warning" onclick="dismissAlert({{ $alert->id }})">
                            <i class="fas fa-times"></i> Ignorer
                        </button>
                    @endif
                    <button class="btn btn-danger" onclick="deleteAlert({{ $alert->id }})">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Informations de l'alerte -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de l'alerte</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted">Recherche sauvegardée</label>
                                <p class="mb-0">
                                    <a href="{{ route('saved-searches.show', $alert->savedSearch) }}" class="text-decoration-none">
                                        {{ $alert->savedSearch->name }}
                                    </a>
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Score de correspondance</label>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $alert->match_level_color }}" 
                                             style="width: {{ $alert->matching_score }}%">
                                            {{ $alert->formatted_score }}
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $alert->match_level_color }}">{{ $alert->match_level_name }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Statut</label>
                                <div>
                                    @if($alert->is_new)
                                        <span class="badge bg-success me-1">Nouveau</span>
                                    @endif
                                    @if($alert->is_read)
                                        <span class="badge bg-secondary me-1">Lu</span>
                                    @else
                                        <span class="badge bg-primary me-1">Non lu</span>
                                    @endif
                                    @if($alert->is_dismissed)
                                        <span class="badge bg-warning">Ignoré</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Date de création</label>
                                <p class="mb-0">{{ $alert->created_at->format('d/m/Y à H:i') }}</p>
                                <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                            </div>

                            @if($alert->read_at)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Date de lecture</label>
                                    <p class="mb-0">{{ $alert->read_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Critères de correspondance -->
                    @if($alert->alert_data && is_array($alert->alert_data))
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-bullseye me-2"></i>Critères correspondants</h6>
                            </div>
                            <div class="card-body">
                                @foreach($alert->formatted_matching_details as $detail)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>{{ $detail }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Profil du prestataire -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">{{ $alert->prestataire->user->name ?? 'Prestataire' }}</h5>
                                <div class="d-flex gap-2">
                                    <a href="{{ $alert->prestataire_profile_url }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-user"></i> Voir le profil
                                    </a>
                                    <a href="{{ $alert->conversation_url }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-comments"></i> Contacter
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Informations de base -->
                                    <div class="mb-4">
                                        <h6 class="text-muted mb-3">Informations générales</h6>
                                        
                                        @if($alert->prestataire->user->email)
                                            <div class="mb-2">
                                                <i class="fas fa-envelope text-muted me-2"></i>
                                                <span>{{ $alert->prestataire->user->email }}</span>
                                            </div>
                                        @endif

                                        @if($alert->prestataire->phone)
                                            <div class="mb-2">
                                                <i class="fas fa-phone text-muted me-2"></i>
                                                <span>{{ $alert->prestataire->phone }}</span>
                                            </div>
                                        @endif

                                        @if($alert->prestataire->city)
                                            <div class="mb-2">
                                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                                <span>{{ $alert->prestataire->city }}, {{ $alert->prestataire->region }}</span>
                                            </div>
                                        @endif

                                        @if($alert->prestataire->experience_years)
                                            <div class="mb-2">
                                                <i class="fas fa-calendar text-muted me-2"></i>
                                                <span>{{ $alert->prestataire->experience_years }} ans d'expérience</span>
                                            </div>
                                        @endif

                                        @if($alert->prestataire->hourly_rate)
                                            <div class="mb-2">
                                                <i class="fas fa-euro-sign text-muted me-2"></i>
                                                <span>{{ $alert->prestataire->hourly_rate }}€/heure</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Note et avis -->
                                    @if($alert->prestataire->average_rating)
                                        <div class="mb-4">
                                            <h6 class="text-muted mb-3">Évaluations</h6>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $alert->prestataire->average_rating)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-muted"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="fw-bold">{{ number_format($alert->prestataire->average_rating, 1) }}</span>
                                                <span class="text-muted ms-1">({{ $alert->prestataire->reviews_count ?? 0 }} avis)</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <!-- Services -->
                                    @if($alert->prestataire->services && $alert->prestataire->services->count() > 0)
                                        <div class="mb-4">
                                            <h6 class="text-muted mb-3">Services proposés</h6>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($alert->prestataire->services->take(6) as $service)
                                                    <span class="badge bg-light text-dark border">{{ $service->name }}</span>
                                                @endforeach
                                                @if($alert->prestataire->services->count() > 6)
                                                    <span class="badge bg-secondary">+{{ $alert->prestataire->services->count() - 6 }} autres</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Disponibilité -->
                                    @if($alert->prestataire->availability)
                                        <div class="mb-4">
                                            <h6 class="text-muted mb-3">Disponibilité</h6>
                                            <span class="badge bg-{{ $alert->prestataire->availability == 'available' ? 'success' : ($alert->prestataire->availability == 'busy' ? 'warning' : 'danger') }}">
                                                @switch($alert->prestataire->availability)
                                                    @case('available')
                                                        Disponible
                                                        @break
                                                    @case('busy')
                                                        Occupé
                                                        @break
                                                    @case('unavailable')
                                                        Indisponible
                                                        @break
                                                    @default
                                                        Non spécifié
                                                @endswitch
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Description -->
                            @if($alert->prestataire->bio)
                                <div class="mt-4">
                                    <h6 class="text-muted mb-3">À propos</h6>
                                    <p class="text-muted">{{ Str::limit($alert->prestataire->bio, 300) }}</p>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex gap-2">
                                    <a href="{{ $alert->prestataire_profile_url }}" class="btn btn-outline-primary">
                                        <i class="fas fa-user"></i> Voir le profil complet
                                    </a>
                                    <a href="{{ $alert->conversation_url }}" class="btn btn-primary">
                                        <i class="fas fa-comments"></i> Envoyer un message
                                    </a>
                                    @if($alert->prestataire->phone)
                                        <a href="tel:{{ $alert->prestataire->phone }}" class="btn btn-outline-success">
                                            <i class="fas fa-phone"></i> Appeler
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead(alertId) {
    fetch(`/api/matching-alerts/${alertId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
}

function dismissAlert(alertId) {
    if (confirm('Êtes-vous sûr de vouloir ignorer cette alerte ?')) {
        fetch(`/api/matching-alerts/${alertId}/dismiss`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("matching-alerts.index") }}';
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}

function deleteAlert(alertId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')) {
        fetch(`/api/matching-alerts/${alertId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("matching-alerts.index") }}';
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}
</script>
@endsection
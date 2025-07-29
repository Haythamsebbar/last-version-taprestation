@extends('layouts.app')

@section('title', 'Mes Alertes de Correspondance')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête avec statistiques -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Mes Alertes de Correspondance</h1>
                    <p class="text-muted mb-0">Gérez vos alertes et découvrez de nouveaux prestataires</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                        <i class="fas fa-check-double"></i> Tout marquer comme lu
                    </button>
                    <button class="btn btn-outline-danger" onclick="clearDismissed()">
                        <i class="fas fa-trash"></i> Supprimer les ignorées
                    </button>
                    <a href="{{ route('matching-alerts.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-download"></i> Exporter CSV
                    </a>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="fas fa-bell fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ $stats['total'] }}</h4>
                            <p class="text-muted mb-0">Total des alertes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ $stats['unread'] }}</h4>
                            <p class="text-muted mb-0">Non lues</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="fas fa-star fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ $stats['high_match'] }}</h4>
                            <p class="text-muted mb-0">Correspondances élevées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ number_format($stats['average_score'], 1) }}%</h4>
                            <p class="text-muted mb-0">Score moyen</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('matching-alerts.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Statut</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Non lues</option>
                                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Lues</option>
                                <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Ignorées</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Nouvelles</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="match_level" class="form-label">Niveau de correspondance</label>
                            <select name="match_level" id="match_level" class="form-select">
                                <option value="">Tous les niveaux</option>
                                <option value="high" {{ request('match_level') == 'high' ? 'selected' : '' }}>Élevé</option>
                                <option value="medium" {{ request('match_level') == 'medium' ? 'selected' : '' }}>Moyen</option>
                                <option value="low" {{ request('match_level') == 'low' ? 'selected' : '' }}>Faible</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="min_score" class="form-label">Score minimum</label>
                            <input type="number" name="min_score" id="min_score" class="form-control" 
                                   min="0" max="100" value="{{ request('min_score') }}" placeholder="0-100">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                            <a href="{{ route('matching-alerts.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des alertes -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($alerts->count() > 0)
                        <div class="row">
                            @foreach($alerts as $alert)
                                <div class="col-12 mb-3">
                                    <div class="card border-start border-4 {{ $alert->is_read ? 'border-secondary' : 'border-primary' }} h-100">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <h6 class="mb-0 me-2">
                                                            <a href="{{ route('matching-alerts.show', $alert) }}" class="text-decoration-none">
                                                                {{ $alert->prestataire->user->name ?? 'Prestataire' }}
                                                            </a>
                                                        </h6>
                                                        @if($alert->is_new)
                                                            <span class="badge bg-success">Nouveau</span>
                                                        @endif
                                                        @if(!$alert->is_read)
                                                            <span class="badge bg-primary ms-1">Non lu</span>
                                                        @endif
                                                        @if($alert->is_dismissed)
                                                            <span class="badge bg-secondary ms-1">Ignoré</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-muted mb-2">
                                                        <i class="fas fa-search me-1"></i>
                                                        Recherche: <strong>{{ $alert->savedSearch->name }}</strong>
                                                    </p>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-{{ $alert->match_level_color }} me-2">
                                                            {{ $alert->formatted_score }} - {{ $alert->match_level_name }}
                                                        </span>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $alert->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('matching-alerts.show', $alert) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                        @if(!$alert->is_read)
                                                            <button class="btn btn-sm btn-outline-success" onclick="markAsRead({{ $alert->id }})">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                        @if(!$alert->is_dismissed)
                                                            <button class="btn btn-sm btn-outline-warning" onclick="dismissAlert({{ $alert->id }})">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteAlert({{ $alert->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $alerts->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune alerte trouvée</h5>
                            <p class="text-muted">Créez des recherches sauvegardées pour recevoir des alertes de correspondance.</p>
                            <a href="{{ route('saved-searches.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Créer une recherche sauvegardée
                            </a>
                        </div>
                    @endif
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
}

function markAllAsRead() {
    if (confirm('Marquer toutes les alertes comme lues ?')) {
        fetch('/api/matching-alerts/mark-all-read', {
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
}

function clearDismissed() {
    if (confirm('Supprimer toutes les alertes ignorées ?')) {
        fetch('/api/matching-alerts/clear-dismissed', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
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
}
</script>
@endsection
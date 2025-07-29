@extends('layouts.admin-modern')

@section('title', 'Signalements Équipements')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Signalements Équipements</h1>
            <p class="text-muted">Gérer les signalements des équipements</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">En cours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['under_review'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Résolus</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['resolved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('administrateur.reports.equipments.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>En cours</option>
                                <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigation</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Résolu</option>
                                <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Rejeté</option>
                                <option value="escalated" {{ request('status') === 'escalated' ? 'selected' : '' }}>Escaladé</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">Toutes les catégories</option>
                                <option value="safety" {{ request('category') === 'safety' ? 'selected' : '' }}>Sécurité</option>
                                <option value="condition" {{ request('category') === 'condition' ? 'selected' : '' }}>État du matériel</option>
                                <option value="fraud" {{ request('category') === 'fraud' ? 'selected' : '' }}>Fraude</option>
                                <option value="inappropriate" {{ request('category') === 'inappropriate' ? 'selected' : '' }}>Contenu inapproprié</option>
                                <option value="pricing" {{ request('category') === 'pricing' ? 'selected' : '' }}>Prix abusif</option>
                                <option value="availability" {{ request('category') === 'availability' ? 'selected' : '' }}>Disponibilité</option>
                                <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Recherche</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Nom équipement, description..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">Filtrer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des signalements -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Signalements ({{ $reports->total() }})</h6>
        </div>
        <div class="card-body">
            @if($reports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Équipement</th>
                                <th>Catégorie</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>
                                        @if($report->equipment)
                                            <a href="{{ route('equipment.show', $report->equipment) }}" target="_blank">
                                                {{ Str::limit($report->equipment->name, 50) }}
                                            </a>
                                            <br><small class="text-muted">{{ $report->reason }}</small>
                                        @else
                                            <span class="text-muted">Équipement supprimé</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($report->category)
                                            @case('safety')
                                                <span class="badge badge-danger">Sécurité</span>
                                                @break
                                            @case('condition')
                                                <span class="badge badge-warning">État</span>
                                                @break
                                            @case('fraud')
                                                <span class="badge badge-dark">Fraude</span>
                                                @break
                                            @case('inappropriate')
                                                <span class="badge badge-secondary">Inapproprié</span>
                                                @break
                                            @case('pricing')
                                                <span class="badge badge-info">Prix</span>
                                                @break
                                            @case('availability')
                                                <span class="badge badge-primary">Disponibilité</span>
                                                @break
                                            @default
                                                <span class="badge badge-light">{{ ucfirst($report->category) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($report->priority)
                                            @case('urgent')
                                                <span class="badge badge-danger">Urgent</span>
                                                @break
                                            @case('high')
                                                <span class="badge badge-warning">Élevée</span>
                                                @break
                                            @case('medium')
                                                <span class="badge badge-info">Moyenne</span>
                                                @break
                                            @case('low')
                                                <span class="badge badge-secondary">Faible</span>
                                                @break
                                            @default
                                                <span class="badge badge-light">{{ ucfirst($report->priority) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($report->status)
                                            @case('pending')
                                                <span class="badge badge-warning">En attente</span>
                                                @break
                                            @case('under_review')
                                                <span class="badge badge-info">En cours</span>
                                                @break
                                            @case('investigating')
                                                <span class="badge badge-primary">Investigation</span>
                                                @break
                                            @case('resolved')
                                                <span class="badge badge-success">Résolu</span>
                                                @break
                                            @case('dismissed')
                                                <span class="badge badge-secondary">Rejeté</span>
                                                @break
                                            @case('escalated')
                                                <span class="badge badge-danger">Escaladé</span>
                                                @break
                                            @default
                                                <span class="badge badge-light">{{ $report->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('administrateur.reports.equipments.show', $report) }}" 
                                           class="btn btn-sm btn-primary">Voir</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $reports->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-tools fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Aucun signalement trouvé.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
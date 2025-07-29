@extends('layouts.admin-modern')

@section('title', 'Signalements Ventes Urgentes')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Signalements Ventes Urgentes</h1>
            <p class="text-muted">Gérer les signalements des annonces de ventes urgentes</p>
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
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Examinés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['reviewed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
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
            <form method="GET" action="{{ route('administrateur.reports.urgent-sales.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Examiné</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Résolu</option>
                                <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="reason">Raison</label>
                            <select name="reason" id="reason" class="form-control">
                                <option value="">Toutes les raisons</option>
                                <option value="inappropriate_content" {{ request('reason') === 'inappropriate_content' ? 'selected' : '' }}>Contenu inapproprié</option>
                                <option value="fraud" {{ request('reason') === 'fraud' ? 'selected' : '' }}>Fraude</option>
                                <option value="spam" {{ request('reason') === 'spam' ? 'selected' : '' }}>Spam</option>
                                <option value="fake_listing" {{ request('reason') === 'fake_listing' ? 'selected' : '' }}>Fausse annonce</option>
                                <option value="other" {{ request('reason') === 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Recherche</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Titre, description, utilisateur..." value="{{ request('search') }}">
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
                                <th>Annonce</th>
                                <th>Utilisateur</th>
                                <th>Raison</th>
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
                                        @if($report->urgentSale)
                                            <a href="{{ route('urgent-sales.show', $report->urgentSale) }}" target="_blank">
                                                {{ Str::limit($report->urgentSale->title, 50) }}
                                            </a>
                                        @else
                                            <span class="text-muted">Annonce supprimée</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->user)
                                            {{ $report->user->name }}
                                            <br><small class="text-muted">{{ $report->user->email }}</small>
                                        @else
                                            <span class="text-muted">Utilisateur supprimé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $report->reason)) }}</span>
                                    </td>
                                    <td>
                                        @switch($report->status)
                                            @case('pending')
                                                <span class="badge badge-warning">En attente</span>
                                                @break
                                            @case('reviewed')
                                                <span class="badge badge-info">Examiné</span>
                                                @break
                                            @case('resolved')
                                                <span class="badge badge-success">Résolu</span>
                                                @break
                                            @case('dismissed')
                                                <span class="badge badge-secondary">Rejeté</span>
                                                @break
                                            @default
                                                <span class="badge badge-light">{{ $report->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('administrateur.reports.urgent-sales.show', $report) }}" 
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
                    <i class="fas fa-flag fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Aucun signalement trouvé.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
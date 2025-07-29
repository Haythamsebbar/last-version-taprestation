@extends('layouts.admin-modern')

@section('title', 'Tous les signalements')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Tous les signalements</h1>
            <p class="text-muted">Vue d'ensemble de tous les signalements</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
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

        <div class="col-xl-2 col-md-4 mb-4">
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

        <div class="col-xl-2 col-md-4 mb-4">
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

        <div class="col-xl-2 col-md-4 mb-4">
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

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ventes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['urgent_sales'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Équipements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['equipments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
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
            <form method="GET" action="{{ route('administrateur.reports.all.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Tous les types</option>
                                <option value="urgent_sales" {{ request('type') === 'urgent_sales' ? 'selected' : '' }}>Ventes urgentes</option>
                                <option value="equipments" {{ request('type') === 'equipments' ? 'selected' : '' }}>Équipements</option>
                            </select>
                        </div>
                    </div>
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
                    <div class="col-md-5">
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
            <h6 class="m-0 font-weight-bold text-primary">Signalements ({{ $allReports->count() }})</h6>
        </div>
        <div class="card-body">
            @if($paginatedReports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Élément</th>
                                <th>Raison</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paginatedReports as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>
                                        @if($report->report_type === 'urgent_sale')
                                            <span class="badge badge-danger">Vente urgente</span>
                                        @else
                                            <span class="badge badge-info">Équipement</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ $report->item_url }}" target="_blank">
                                            {{ Str::limit($report->item_title, 50) }}
                                        </a>
                                        @if($report->report_type === 'urgent_sale' && $report->user)
                                            <br><small class="text-muted">Par: {{ $report->user->name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->report_type === 'urgent_sale')
                                            <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $report->reason)) }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $report->reason }}</span>
                                            @if($report->category)
                                                <br><small class="text-muted">{{ ucfirst($report->category) }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @switch($report->status)
                                            @case('pending')
                                                <span class="badge badge-warning">En attente</span>
                                                @break
                                            @case('reviewed')
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
                                        @if($report->report_type === 'urgent_sale')
                                            <a href="{{ route('administrateur.reports.urgent-sales.show', $report) }}" 
                                               class="btn btn-sm btn-primary">Voir</a>
                                        @else
                                            <a href="{{ route('administrateur.reports.equipments.show', $report) }}" 
                                               class="btn btn-sm btn-primary">Voir</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination simple -->
                @if($allReports->count() > 20)
                    <div class="d-flex justify-content-center mt-3">
                        <nav>
                            <ul class="pagination">
                                @php
                                    $currentPage = request()->get('page', 1);
                                    $totalPages = ceil($allReports->count() / 20);
                                @endphp
                                
                                @if($currentPage > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}">Précédent</a>
                                    </li>
                                @endif
                                
                                @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                                
                                @if($currentPage < $totalPages)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}">Suivant</a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif
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
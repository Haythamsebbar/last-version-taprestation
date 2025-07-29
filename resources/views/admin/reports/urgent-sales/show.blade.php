@extends('layouts.admin-modern')

@section('title', 'Signalement #' . $report->id)

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Signalement #{{ $report->id }}</h1>
            <p class="text-muted">Détails du signalement de vente urgente</p>
        </div>
        <a href="{{ route('administrateur.reports.urgent-sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="row">
        <!-- Informations du signalement -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Détails du signalement</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Raison du signalement</h6>
                            <p class="mb-3">
                                <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $report->reason)) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Statut</h6>
                            <p class="mb-3">
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
                            </p>
                        </div>
                    </div>

                    @if($report->description)
                        <h6 class="font-weight-bold">Description</h6>
                        <p class="mb-3">{{ $report->description }}</p>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Date du signalement</h6>
                            <p class="mb-3">{{ $report->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        @if($report->reviewed_at)
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Date de traitement</h6>
                                <p class="mb-3">{{ $report->reviewed_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        @endif
                    </div>

                    @if($report->admin_notes)
                        <h6 class="font-weight-bold">Notes administrateur</h6>
                        <div class="alert alert-info">
                            {{ $report->admin_notes }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations sur l'annonce -->
            @if($report->urgentSale)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Annonce signalée</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>{{ $report->urgentSale->title }}</h5>
                                <p class="text-muted mb-2">{{ Str::limit($report->urgentSale->description, 200) }}</p>
                                <p class="mb-2">
                                    <strong>Prix:</strong> {{ number_format($report->urgentSale->price, 0, ',', ' ') }} €
                                </p>
                                <p class="mb-2">
                                    <strong>Localisation:</strong> {{ $report->urgentSale->city }}
                                </p>
                                <p class="mb-2">
                                    <strong>Publié le:</strong> {{ $report->urgentSale->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                @if($report->urgentSale->photos && count($report->urgentSale->photos) > 0)
                                    <img src="{{ Storage::url($report->urgentSale->photos[0]) }}" 
                                         alt="Photo de l'annonce" class="img-fluid rounded">
                                @endif
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('urgent-sales.show', $report->urgentSale) }}" 
                               class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Voir l'annonce
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5>Annonce supprimée</h5>
                        <p class="text-muted">L'annonce associée à ce signalement a été supprimée.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="col-lg-4">
            <!-- Informations sur l'utilisateur -->
            @if($report->user)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Utilisateur signalant</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="avatar-circle bg-primary text-white mb-2">
                                {{ strtoupper(substr($report->user->name, 0, 2)) }}
                            </div>
                            <h6>{{ $report->user->name }}</h6>
                            <p class="text-muted">{{ $report->user->email }}</p>
                        </div>
                        <hr>
                        <p class="mb-2">
                            <strong>Membre depuis:</strong><br>
                            {{ $report->user->created_at->format('d/m/Y') }}
                        </p>
                        <p class="mb-2">
                            <strong>Signalements effectués:</strong><br>
                            {{ \App\Models\UrgentSaleReport::where('user_id', $report->user->id)->count() }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Actions administrateur -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('administrateur.reports.urgent-sales.update-status', $report) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group">
                            <label for="status">Changer le statut</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="reviewed" {{ $report->status === 'reviewed' ? 'selected' : '' }}>Examiné</option>
                                <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Résolu</option>
                                <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_notes">Notes administrateur</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="4" 
                                      placeholder="Ajoutez vos notes sur ce signalement...">{{ $report->admin_notes }}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                    </form>
                    
                    <hr>
                    
                    <form action="{{ route('administrateur.reports.urgent-sales.destroy', $report) }}" 
                          method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce signalement ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Supprimer le signalement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: bold;
    margin: 0 auto;
}
</style>
@endsection
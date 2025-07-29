@extends('layouts.admin-modern')

@section('title', 'Signalement #' . $report->id)

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Signalement #{{ $report->id }}</h1>
            <p class="text-muted">Détails du signalement d'équipement</p>
        </div>
        <a href="{{ route('administrateur.reports.equipments.index') }}" class="btn btn-secondary">
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
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">Catégorie</h6>
                            <p class="mb-3">
                                @switch($report->category)
                                    @case('safety')
                                        <span class="badge badge-danger">Sécurité</span>
                                        @break
                                    @case('condition')
                                        <span class="badge badge-warning">État du matériel</span>
                                        @break
                                    @case('fraud')
                                        <span class="badge badge-dark">Fraude</span>
                                        @break
                                    @case('inappropriate')
                                        <span class="badge badge-secondary">Contenu inapproprié</span>
                                        @break
                                    @case('pricing')
                                        <span class="badge badge-info">Prix abusif</span>
                                        @break
                                    @case('availability')
                                        <span class="badge badge-primary">Disponibilité</span>
                                        @break
                                    @default
                                        <span class="badge badge-light">{{ ucfirst($report->category) }}</span>
                                @endswitch
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">Priorité</h6>
                            <p class="mb-3">
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
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">Statut</h6>
                            <p class="mb-3">
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
                            </p>
                        </div>
                    </div>

                    <h6 class="font-weight-bold">Raison du signalement</h6>
                    <p class="mb-3">{{ $report->reason }}</p>

                    @if($report->description)
                        <h6 class="font-weight-bold">Description détaillée</h6>
                        <p class="mb-3">{{ $report->description }}</p>
                    @endif

                    @if($report->evidence_photos && count($report->evidence_photos) > 0)
                        <h6 class="font-weight-bold">Photos de preuve</h6>
                        <div class="row mb-3">
                            @foreach($report->evidence_photos as $photo)
                                <div class="col-md-3 mb-2">
                                    <img src="{{ Storage::url($photo) }}" alt="Preuve" class="img-fluid rounded" 
                                         data-toggle="modal" data-target="#photoModal{{ $loop->index }}" style="cursor: pointer;">
                                    
                                    <!-- Modal pour agrandir la photo -->
                                    <div class="modal fade" id="photoModal{{ $loop->index }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Photo de preuve {{ $loop->index + 1 }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ Storage::url($photo) }}" alt="Preuve" class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Date du signalement</h6>
                            <p class="mb-3">{{ $report->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        @if($report->resolved_at)
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Date de résolution</h6>
                                <p class="mb-3">{{ $report->resolved_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        @endif
                    </div>

                    @if($report->admin_notes)
                        <h6 class="font-weight-bold">Notes administrateur</h6>
                        <div class="alert alert-info">
                            {{ $report->admin_notes }}
                        </div>
                    @endif

                    @if($report->resolution)
                        <h6 class="font-weight-bold">Résolution</h6>
                        <div class="alert alert-success">
                            {{ $report->resolution }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations sur l'équipement -->
            @if($report->equipment)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Équipement signalé</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>{{ $report->equipment->name }}</h5>
                                <p class="text-muted mb-2">{{ Str::limit($report->equipment->description, 200) }}</p>
                                <p class="mb-2">
                                    <strong>Prix par jour:</strong> {{ number_format($report->equipment->price_per_day, 0, ',', ' ') }} €
                                </p>
                                <p class="mb-2">
                                    <strong>Localisation:</strong> {{ $report->equipment->city }}
                                </p>
                                <p class="mb-2">
                                    <strong>État:</strong> 
                                    @switch($report->equipment->condition)
                                        @case('excellent')
                                            <span class="badge badge-success">Excellent</span>
                                            @break
                                        @case('good')
                                            <span class="badge badge-info">Bon</span>
                                            @break
                                        @case('fair')
                                            <span class="badge badge-warning">Correct</span>
                                            @break
                                        @case('poor')
                                            <span class="badge badge-danger">Mauvais</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ ucfirst($report->equipment->condition) }}</span>
                                    @endswitch
                                </p>
                                <p class="mb-2">
                                    <strong>Publié le:</strong> {{ $report->equipment->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                @if($report->equipment->main_photo)
                                    <img src="{{ Storage::url($report->equipment->main_photo) }}" 
                                         alt="Photo de l'équipement" class="img-fluid rounded">
                                @endif
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('equipment.show', $report->equipment) }}" 
                               class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Voir l'équipement
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5>Équipement supprimé</h5>
                        <p class="text-muted">L'équipement associé à ce signalement a été supprimé.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="col-lg-4">
            <!-- Informations sur le rapporteur -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du rapporteur</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Type:</strong> 
                        @switch($report->reporter_type)
                            @case('client')
                                <span class="badge badge-primary">Client</span>
                                @break
                            @case('prestataire')
                                <span class="badge badge-info">Prestataire</span>
                                @break
                            @case('anonymous')
                                <span class="badge badge-secondary">Anonyme</span>
                                @break
                            @default
                                <span class="badge badge-light">{{ ucfirst($report->reporter_type) }}</span>
                        @endswitch
                    </p>
                    
                    @if($report->contact_info && count($report->contact_info) > 0)
                        <h6 class="font-weight-bold mt-3">Informations de contact</h6>
                        @foreach($report->contact_info as $key => $value)
                            <p class="mb-1"><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</p>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Actions administrateur -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('administrateur.reports.equipments.update-status', $report) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group">
                            <label for="status">Changer le statut</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="under_review" {{ $report->status === 'under_review' ? 'selected' : '' }}>En cours</option>
                                <option value="investigating" {{ $report->status === 'investigating' ? 'selected' : '' }}>Investigation</option>
                                <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Résolu</option>
                                <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>Rejeté</option>
                                <option value="escalated" {{ $report->status === 'escalated' ? 'selected' : '' }}>Escaladé</option>
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
                    
                    <form action="{{ route('administrateur.reports.equipments.destroy', $report) }}" 
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
@endsection
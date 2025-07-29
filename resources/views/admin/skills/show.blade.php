@extends('layouts.admin-modern')

@section('title', 'Détails de la compétence')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de la compétence</h1>
        <div>
            <a href="{{ route('administrateur.skills.edit', $skill) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
            <button type="button" class="btn btn-danger ml-2" data-toggle="modal" data-target="#deleteModal">
                <i class="fas fa-trash mr-1"></i> Supprimer
            </button>
            <a href="{{ route('administrateur.skills.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">ID</th>
                                <td>{{ $skill->id }}</td>
                            </tr>
                            <tr>
                                <th>Nom</th>
                                <td>{{ $skill->name }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $skill->description ?: 'Aucune description' }}</td>
                            </tr>
                            <tr>
                                <th>Date de création</th>
                                <td>{{ $skill->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Dernière modification</th>
                                <td>{{ $skill->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Prestataires associés</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $skill->prestataires->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prestataires associés -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Prestataires associés</h6>
        </div>
        <div class="card-body">
            @if($skill->prestataires->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Statut</th>
                                <th>Services</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($skill->prestataires as $prestataire)
                                <tr>
                                    <td>{{ $prestataire->id }}</td>
                                    <td>{{ $prestataire->user->name }}</td>
                                    <td>{{ $prestataire->user->email }}</td>
                                    <td>
                                        @if($prestataire->is_approved)
                                            <span class="badge badge-success">Approuvé</span>
                                        @else
                                            <span class="badge badge-warning">En attente</span>
                                        @endif
                                        @if($prestataire->user->is_blocked)
                                            <span class="badge badge-danger">Bloqué</span>
                                        @endif
                                    </td>
                                    <td>{{ $prestataire->services->count() }}</td>
                                    <td>{{ $prestataire->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('administrateur.prestataires.show', $prestataire) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> Aucun prestataire associé à cette compétence.
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer la compétence <strong>{{ $skill->name }}</strong> ?</p>
                    @if($skill->prestataires->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Cette compétence est associée à {{ $skill->prestataires->count() }} prestataire(s). La suppression affectera ces prestataires.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <form action="{{ route('administrateur.skills.destroy', $skill) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
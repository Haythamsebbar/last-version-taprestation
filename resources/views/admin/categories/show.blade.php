@extends('layouts.admin-modern')

@section('title', 'Détails de la catégorie')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de la catégorie</h1>
        <div>
            <a href="{{ route('administrateur.categories.edit', $category) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
            <button type="button" class="btn btn-danger ml-2" data-toggle="modal" data-target="#deleteModal">
                <i class="fas fa-trash mr-1"></i> Supprimer
            </button>
            <a href="{{ route('administrateur.categories.index') }}" class="btn btn-secondary ml-2">
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
                                <td>{{ $category->id }}</td>
                            </tr>
                            <tr>
                                <th>Nom</th>
                                <td>{{ $category->name }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $category->description ?: 'Aucune description' }}</td>
                            </tr>
                            <tr>
                                <th>Catégorie parente</th>
                                <td>
                                    @if($category->parent)
                                        <a href="{{ route('administrateur.categories.show', $category->parent) }}">
                                            {{ $category->parent->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Aucune (catégorie principale)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Date de création</th>
                                <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Dernière modification</th>
                                <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
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
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sous-catégories</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category->children->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Services associés</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category->services->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
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

    <!-- Sous-catégories -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Sous-catégories</h6>
            <a href="{{ route('administrateur.categories.create', ['parent_id' => $category->id]) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Ajouter une sous-catégorie
            </a>
        </div>
        <div class="card-body">
            @if($category->children->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Services</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->children as $child)
                                <tr>
                                    <td>{{ $child->id }}</td>
                                    <td>{{ $child->name }}</td>
                                    <td>{{ Str::limit($child->description, 50) }}</td>
                                    <td>{{ $child->services->count() }}</td>
                                    <td>{{ $child->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administrateur.categories.show', $child) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('administrateur.categories.edit', $child) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteChildModal{{ $child->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de suppression -->
                                        <div class="modal fade" id="deleteChildModal{{ $child->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteChildModalLabel{{ $child->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteChildModalLabel{{ $child->id }}">Confirmer la suppression</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Êtes-vous sûr de vouloir supprimer la sous-catégorie <strong>{{ $child->name }}</strong> ?</p>
                                                        @if($child->services->count() > 0)
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                                Cette sous-catégorie est associée à {{ $child->services->count() }} service(s). La suppression affectera ces services.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('administrateur.categories.destroy', $child) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> Aucune sous-catégorie trouvée.
                </div>
            @endif
        </div>
    </div>

    <!-- Services associés -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Services associés</h6>
        </div>
        <div class="card-body">
            @if($category->services->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Prestataire</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->services as $service)
                                <tr>
                                    <td>{{ $service->id }}</td>
                                    <td>{{ $service->title }}</td>
                                    <td>
                                        <a href="{{ route('administrateur.prestataires.show', $service->prestataire) }}">
                                            {{ $service->prestataire->user->name }}
                                        </a>
                                    </td>
                                    <td>{{ $service->price }} €</td>
                                    <td>
                                        @if($service->is_visible)
                                            <span class="badge badge-success">Visible</span>
                                        @else
                                            <span class="badge badge-danger">Masqué</span>
                                        @endif
                                    </td>
                                    <td>{{ $service->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('administrateur.services.show', $service) }}" class="btn btn-info btn-sm">
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
                    <i class="fas fa-info-circle mr-1"></i> Aucun service associé à cette catégorie.
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
                    <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ $category->name }}</strong> ?</p>
                    @if($category->children->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Cette catégorie possède {{ $category->children->count() }} sous-catégorie(s). La suppression affectera également ces sous-catégories.
                        </div>
                    @endif
                    @if($category->services->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Cette catégorie est associée à {{ $category->services->count() }} service(s). La suppression affectera ces services.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <form action="{{ route('administrateur.categories.destroy', $category) }}" method="POST">
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
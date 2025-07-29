@extends('layouts.admin-modern')

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Prestataires en attente d'approbation</h6>
            <div>
                <a href="{{ route('administrateur.prestataires.index') }}" class="btn btn-sm btn-primary me-2">
                    <i class="bi bi-list"></i> Tous les prestataires
                </a>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="bi bi-funnel"></i> Filtres
                </button>
            </div>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body bg-light">
                <form action="{{ route('administrateur.prestataires.pending') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="{{ request('email') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="secteur" class="form-label">Secteur d'activité</label>
                        <input type="text" class="form-control" id="secteur" name="secteur" value="{{ request('secteur') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Trier par</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date d'inscription</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom</option>
                            <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="secteur_activite" {{ request('sort') == 'secteur_activite' ? 'selected' : '' }}>Secteur d'activité</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="direction" class="form-label">Ordre</label>
                        <select class="form-select" id="direction" name="direction">
                            <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                            <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Croissant</option>
                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <a href="{{ route('administrateur.prestataires.pending') }}" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($prestataires->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Secteur d'activité</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prestataires as $prestataire)
                                <tr>
                                    <td>{{ $prestataire->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($prestataire->user->profile_photo_url)
                                                <img src="{{ $prestataire->user->profile_photo_url }}" alt="Photo" class="rounded-circle me-2" width="40" height="40">
                                            @else
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person text-white"></i>
                                                </div>
                                            @endif
                                            {{ $prestataire->user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $prestataire->user->email }}</td>
                                    <td>{{ $prestataire->secteur_activite }}</td>
                                    <td>{{ $prestataire->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administrateur.prestataires.show', $prestataire->id) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(auth()->id() != $prestataire->user_id)
                                                <form action="{{ route('administrateur.prestataires.approve', $prestataire->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approuver">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('administrateur.prestataires.toggle-block', $prestataire->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $prestataire->user->blocked_at ? 'btn-success' : 'btn-secondary' }}" title="{{ $prestataire->user->blocked_at ? 'Débloquer' : 'Bloquer' }}">
                                                        <i class="bi {{ $prestataire->user->blocked_at ? 'bi-unlock' : 'bi-lock' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('administrateur.prestataires.destroy', $prestataire->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce prestataire ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $prestataires->appends(request()->query())->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    Aucun prestataire en attente d'approbation.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
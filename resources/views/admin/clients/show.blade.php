@extends('layouts.admin-modern')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('administrateur.clients.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Détails du client</h6>
                    <div>
                        @if(auth()->id() != $client->user_id)
                            <form action="{{ route('administrateur.clients.toggle-block', $client->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $client->user->blocked_at ? 'btn-success' : 'btn-warning' }}">
                                    <i class="bi {{ $client->user->blocked_at ? 'bi-unlock' : 'bi-lock' }}"></i> 
                                    {{ $client->user->blocked_at ? 'Débloquer' : 'Bloquer' }}
                                </button>
                            </form>
                            <form action="{{ route('administrateur.clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-circle">
                                        <span class="initials">{{ substr($client->user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">{{ $client->user->name }}</h5>
                                    <p class="text-muted mb-0">{{ $client->user->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Statut:</strong>
                                @if($client->user->blocked_at)
                                    <span class="badge bg-danger">Bloqué depuis le {{ $client->user->blocked_at->format('d/m/Y à H:i') }}</span>
                                @else
                                    <span class="badge bg-success">Actif</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Date d'inscription</h5>
                            <p>{{ $client->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Dernière mise à jour</h5>
                            <p>{{ $client->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Statistiques</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $client->clientRequests->count() }}</h5>
                                        <p class="card-text">Demandes de services</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $client->reviews->count() }}</h5>
                                        <p class="card-text">Avis publiés</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $client->follows->count() }}</h5>
                                        <p class="card-text">Prestataires suivis</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Dernières demandes</h6>
                </div>
                <div class="card-body">
                    @if($client->clientRequests->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($client->clientRequests->sortByDesc('created_at')->take(5) as $request)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;">{{ $request->title }}</span>
                                            <small class="text-muted d-block">{{ $request->created_at->format('d/m/Y') }}</small>
                                        </div>
                                        <span class="badge {{ $request->status == 'pending' ? 'bg-warning' : ($request->status == 'completed' ? 'bg-success' : 'bg-primary') }}">
                                            {{ $request->status == 'pending' ? 'En attente' : ($request->status == 'completed' ? 'Terminée' : 'En cours') }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aucune demande de service</p>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Derniers avis</h6>
                </div>
                <div class="card-body">
                    @if($client->reviews->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($client->reviews->sortByDesc('created_at')->take(5) as $review)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="d-flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi {{ $i <= $review->rating ? 'bi-star-fill text-warning' : 'bi-star' }} small"></i>
                                                @endfor
                                            </div>
                                            <small class="text-muted d-block">{{ $review->created_at->format('d/m/Y') }}</small>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;">{{ $review->comment }}</span>
                                        </div>
                                        <a href="{{ route('administrateur.reviews.show', $review->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aucun avis publié</p>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Prestataires suivis</h6>
                </div>
                <div class="card-body">
                    @if($client->follows->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($client->follows as $prestataire)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span>{{ $prestataire->user->name }}</span>
                                            <small class="text-muted d-block">{{ $prestataire->sector }}</small>
                                        </div>
                                        <a href="{{ route('administrateur.prestataires.show', $prestataire->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aucun prestataire suivi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 50px;
        height: 50px;
        background-color: #007bff;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
    }
    .bi.small {
        font-size: 0.8rem;
    }
</style>
@endsection
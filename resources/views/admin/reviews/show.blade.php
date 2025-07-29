@extends('layouts.admin-modern')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('administrateur.reviews.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Détails de l'avis #{{ $review->id }}</h6>
                    <div>
                        @if(!$review->moderated_by)
                            <form action="{{ route('administrateur.reviews.moderate', $review->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success" title="Marquer comme modéré">
                                    <i class="bi bi-check-lg"></i> Marquer comme modéré
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('administrateur.reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Note</h5>
                        <div class="d-flex mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill text-warning' : 'bi-star' }} me-1" style="font-size: 1.5rem;"></i>
                            @endfor
                            <span class="ms-2 fw-bold">{{ $review->rating }}/5</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Commentaire</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $review->comment }}
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Date de création</h5>
                            <p>{{ $review->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Dernière mise à jour</h5>
                            <p>{{ $review->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Statut de modération</h5>
                        @if($review->moderated_by)
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">Modéré</span>
                                <span>par {{ $review->moderator->name ?? 'Administrateur' }} le {{ $review->updated_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        @else
                            <span class="badge bg-warning">Non modéré</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">Client</h6>
                        </div>
                        <div class="card-body">
                            @if($review->client)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-circle">
                                            <span class="initials">{{ substr($review->client_name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="mb-0">{{ $review->client_name }}</h5>
                                        <p class="text-muted mb-0">{{ $review->client_email ?? 'Email non disponible' }}</p>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong>Inscrit le:</strong> {{ $review->client->created_at->format('d/m/Y') }}
                                </div>
                                <div class="mb-2">
                                    <strong>Nombre d'avis:</strong> {{ $review->client->client && $review->client->client ? $review->client->client->reviews->count() : 0 }}
                                </div>
                                <div class="mt-3">
                                    @if($review->client->client)
                                    <a href="{{ route('administrateur.clients.show', $review->client->client->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-person"></i> Voir le profil
                                    </a>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    Client non disponible ou supprimé.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">Prestataire</h6>
                        </div>
                        <div class="card-body">
                            @if($review->prestataire && $review->prestataire->user)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-circle">
                                            <span class="initials">{{ substr($review->prestataire->user->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="mb-0">{{ $review->prestataire->user->name }}</h5>
                                        <p class="text-muted mb-0">{{ $review->prestataire->user->email }}</p>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong>Secteur:</strong> {{ $review->prestataire->sector ?? 'Non spécifié' }}
                                </div>
                                <div class="mb-2">
                                    <strong>Localisation:</strong> {{ $review->prestataire->location ?? 'Non spécifiée' }}
                                </div>
                                <div class="mb-2">
                                    <strong>Statut:</strong>
                                    @if($review->prestataire->approved_at)
                                        <span class="badge bg-success">Approuvé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('administrateur.prestataires.show', $review->prestataire->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-person"></i> Voir le profil
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    Prestataire non disponible ou supprimé.
                                </div>
                            @endif
                        </div>
                    </div>
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
</style>
@endsection
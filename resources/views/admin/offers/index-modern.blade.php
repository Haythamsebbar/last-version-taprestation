@extends('layouts.admin-modern')

@section('title', 'Gestion des Offres')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestion des Offres</h1>
            <p class="mb-0 text-muted">Gérez toutes les offres soumises par les prestataires</p>
        </div>
        <div>
            <a href="{{ route('administrateur.offers.export', request()->query()) }}" class="btn btn-success me-2">
                <i class="fas fa-download"></i> Exporter
            </a>
            <a href="{{ route('administrateur.offers.analytics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Analyses
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Offres</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Acceptées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['accepted']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rejetées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['rejected']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Prix Moyen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['avg_price'], 2) }}€</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Taux Conversion</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['conversion_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de Recherche</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('administrateur.offers.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Acceptée</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="price_min">Prix Min (€)</label>
                            <input type="number" name="price_min" id="price_min" class="form-control" value="{{ request('price_min') }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="price_max">Prix Max (€)</label>
                            <input type="number" name="price_max" id="price_max" class="form-control" value="{{ request('price_max') }}" placeholder="10000">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_from">Date Début</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_to">Date Fin</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="prestataire">Prestataire</label>
                            <input type="text" name="prestataire" id="prestataire" class="form-control" value="{{ request('prestataire') }}" placeholder="Nom du prestataire">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="search">Recherche dans le message</label>
                            <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Rechercher dans les messages...">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                            <a href="{{ route('administrateur.offers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des offres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Offres ({{ $offers->total() }} résultats)</h6>
        </div>
        <div class="card-body">
            @if($offers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Prestataire</th>
                                <th>Demande Client</th>
                                <th>Prix</th>
                                <th>Délai</th>
                                <th>Statut</th>
                                <th>Date Création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offers as $offer)
                                <tr>
                                    <td>{{ $offer->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                @if($offer->prestataire && $offer->prestataire->user && $offer->prestataire->user->profile_photo)
                                                    <img src="{{ asset('storage/' . $offer->prestataire->user->profile_photo) }}" alt="Photo" class="rounded-circle" width="40" height="40">
                                                @else
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $offer->prestataire->user->name ?? 'N/A' }}</div>
                                                <div class="text-muted small">{{ $offer->prestataire->user->email ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-weight-bold">{{ Str::limit($offer->clientRequest->title ?? 'N/A', 30) }}</div>
                                            <div class="text-muted small">{{ $offer->clientRequest->client->user->name ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-success">{{ number_format($offer->price ?? 0, 2) }}€</span>
                                    </td>
                                    <td>
                                        @if($offer->estimated_duration)
                                            {{ $offer->estimated_duration }} jour(s)
                                        @else
                                            <span class="text-muted">Non spécifié</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($offer->status)
                                            @case('pending')
                                                <span class="badge badge-warning">En attente</span>
                                                @break
                                            @case('accepted')
                                                <span class="badge badge-success">Acceptée</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge badge-danger">Rejetée</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($offer->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div>{{ $offer->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $offer->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administrateur.offers.show', $offer->id) }}" class="btn btn-sm btn-info" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $offer->id }})" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Affichage de {{ $offers->firstItem() }} à {{ $offers->lastItem() }} sur {{ $offers->total() }} résultats
                    </div>
                    <div>
                        {{ $offers->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-handshake fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Aucune offre trouvée</h5>
                    <p class="text-muted">Aucune offre ne correspond aux critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette offre ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(offerId) {
    const form = document.getElementById('deleteForm');
    form.action = `/administrateur/offers/${offerId}`;
    $('#deleteModal').modal('show');
}

// Auto-submit form on filter change
$(document).ready(function() {
    $('#status').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
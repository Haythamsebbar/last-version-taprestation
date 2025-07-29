@extends('layouts.admin-modern')

@section('title', 'Gestion des vérifications')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Gestion des vérifications</h1>
                <div class="btn-group" role="group">
                    <form action="{{ route('admin.verifications.run-automatic') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Lancer la vérification automatique pour tous les prestataires éligibles ?')">
                            <i class="fas fa-magic"></i> Vérification automatique
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Demandes en attente
                            </div>
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approuvées ce mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejetées ce mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Prestataires vérifiés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages de session -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.verifications.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvées</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetées</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="document_type">Type de document</label>
                            <select name="document_type" id="document_type" class="form-control">
                                <option value="">Tous les types</option>
                                <option value="identity" {{ request('document_type') == 'identity' ? 'selected' : '' }}>Pièce d'identité</option>
                                <option value="professional" {{ request('document_type') == 'professional' ? 'selected' : '' }}>Document professionnel</option>
                                <option value="business" {{ request('document_type') == 'business' ? 'selected' : '' }}>Document d'entreprise</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Rechercher</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Nom du prestataire..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                                <a href="{{ route('admin.verifications.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des demandes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Demandes de vérification</h6>
        </div>
        <div class="card-body">
            @if($verificationRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Prestataire</th>
                                <th>Type de document</th>
                                <th>Date de soumission</th>
                                <th>Statut</th>
                                <th>Documents</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($verificationRequests as $request)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($request->prestataire->profile_photo)
                                                <img src="{{ Storage::url($request->prestataire->profile_photo) }}" 
                                                     class="rounded-circle mr-2" width="40" height="40">
                                            @else
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-2" 
                                                     style="width: 40px; height: 40px;">
                                                    <span class="text-white font-weight-bold">
                                                        {{ substr($request->prestataire->nom, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-weight-bold">{{ $request->prestataire->nom }} {{ $request->prestataire->prenom }}</div>
                                                <small class="text-muted">{{ $request->prestataire->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($request->document_type)
                                            @case('identity')
                                                <span class="badge badge-info">Pièce d'identité</span>
                                                @break
                                            @case('professional')
                                                <span class="badge badge-primary">Document professionnel</span>
                                                @break
                                            @case('business')
                                                <span class="badge badge-secondary">Document d'entreprise</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $request->submitted_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($request->isPending())
                                            <span class="badge badge-warning">En attente</span>
                                        @elseif($request->isApproved())
                                            <span class="badge badge-success">Approuvée</span>
                                        @else
                                            <span class="badge badge-danger">Rejetée</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light">{{ count($request->documents ?? []) }} document(s)</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.verifications.show', $request) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($request->isPending())
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="approveRequest({{ $request->id }})" title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="rejectRequest({{ $request->id }})" title="Rejeter">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            
                                            @if($request->prestataire->isVerified())
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="revokeVerification({{ $request->prestataire->id }})" title="Révoquer la vérification">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $verificationRequests->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Aucune demande de vérification trouvée.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'approbation -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approuver la demande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approve_comment">Commentaire (optionnel)</label>
                        <textarea name="admin_comment" id="approve_comment" class="form-control" rows="3" 
                                  placeholder="Commentaire pour le prestataire..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approuver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeter la demande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reject_comment">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea name="admin_comment" id="reject_comment" class="form-control" rows="3" 
                                  placeholder="Expliquez pourquoi la demande est rejetée..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Rejeter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function approveRequest(requestId) {
    // Bootstrap 5 syntax
    document.getElementById('approveForm').setAttribute('action', `/admin/verifications/${requestId}/approve`);
    const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
    approveModal.show();
}

function rejectRequest(requestId) {
    // Bootstrap 5 syntax
    document.getElementById('rejectForm').setAttribute('action', `/admin/verifications/${requestId}/reject`);
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    rejectModal.show();
}

function revokeVerification(prestataireId) {
    if (confirm('Êtes-vous sûr de vouloir révoquer la vérification de ce prestataire ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/verifications/${prestataireId}/revoke`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
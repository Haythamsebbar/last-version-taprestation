@extends('layouts.admin-modern')

@section('title', 'Détails de la demande de vérification')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Détails de la demande de vérification</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.verifications.index') }}">Vérifications</a></li>
                            <li class="breadcrumb-item active">Demande #{{ $verificationRequest->id }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.verifications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
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

    <div class="row">
        <!-- Informations du prestataire -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du prestataire</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($verificationRequest->prestataire->profile_photo)
                            <img src="{{ Storage::url($verificationRequest->prestataire->profile_photo) }}" 
                                 class="rounded-circle" width="80" height="80">
                        @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                 style="width: 80px; height: 80px;">
                                <span class="text-white font-weight-bold h4 mb-0">
                                    {{ substr($verificationRequest->prestataire->nom, 0, 1) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="text-center mb-3">
                        <h5 class="font-weight-bold">{{ $verificationRequest->prestataire->nom }} {{ $verificationRequest->prestataire->prenom }}</h5>
                        <p class="text-muted mb-1">{{ $verificationRequest->prestataire->user->email }}</p>
                        @if($verificationRequest->prestataire->telephone)
                            <p class="text-muted mb-1">{{ $verificationRequest->prestataire->telephone }}</p>
                        @endif
                        @if($verificationRequest->prestataire->isVerified())
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Vérifié</span>
                        @else
                            <span class="badge badge-secondary">Non vérifié</span>
                        @endif
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h6 class="font-weight-bold text-primary">{{ $verificationRequest->prestataire->average_rating ?? 'N/A' }}</h6>
                                <small class="text-muted">Note moyenne</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="font-weight-bold text-primary">{{ $verificationRequest->prestataire->reviews()->count() }}</h6>
                            <small class="text-muted">Avis</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-2">
                        <strong>Secteur d'activité:</strong>
                        <span class="text-muted">{{ $verificationRequest->prestataire->secteur_activite ?? 'Non spécifié' }}</span>
                    </div>
                    
                    <div class="mb-2">
                        <strong>Date d'inscription:</strong>
                        <span class="text-muted">{{ $verificationRequest->prestataire->created_at->format('d/m/Y') }}</span>
                    </div>
                    
                    
                    
                    
                </div>
            </div>
        </div>
        
        <!-- Détails de la demande -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Détails de la demande</h6>
                    <div>
                        @if($verificationRequest->isPending())
                            <span class="badge badge-warning badge-lg">En attente</span>
                        @elseif($verificationRequest->isApproved())
                            <span class="badge badge-success badge-lg">Approuvée</span>
                        @else
                            <span class="badge badge-danger badge-lg">Rejetée</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>ID de la demande:</strong>
                            <span class="text-muted">#{{ $verificationRequest->id }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Type de document:</strong>
                            @switch($verificationRequest->document_type)
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
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Date de soumission:</strong>
                            <span class="text-muted">{{ $verificationRequest->submitted_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @if($verificationRequest->reviewed_at)
                            <div class="col-md-6">
                                <strong>Date de révision:</strong>
                                <span class="text-muted">{{ $verificationRequest->reviewed_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        @endif
                    </div>
                    
                    @if($verificationRequest->reviewedBy)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Révisé par:</strong>
                                <span class="text-muted">{{ $verificationRequest->reviewedBy->name }}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if($verificationRequest->admin_comment)
                        <div class="mb-3">
                            <strong>Commentaire administrateur:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {{ $verificationRequest->admin_comment }}
                            </div>
                        </div>
                    @endif
                    
                    <!-- Actions -->
                    @if($verificationRequest->isPending())
                        <div class="mt-4">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" onclick="approveRequest()">
                                    <i class="fas fa-check"></i> Approuver
                                </button>
                                <button type="button" class="btn btn-danger" onclick="rejectRequest()">
                                    <i class="fas fa-times"></i> Rejeter
                                </button>
                            </div>
                        </div>
                    @endif
                    
                    @if($verificationRequest->prestataire->isVerified())
                        <div class="mt-4">
                            <button type="button" class="btn btn-warning" onclick="revokeVerification()">
                                <i class="fas fa-ban"></i> Révoquer la vérification
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Documents soumis -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Documents soumis</h6>
                </div>
                <div class="card-body">
                    @if($verificationRequest->documents && count($verificationRequest->documents) > 0)
                        <div class="row">
                            @foreach($verificationRequest->documents as $index => $document)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border h-100">
                                        <div class="card-body text-center p-3 d-flex flex-column">
                                            <div class="mb-3 flex-grow-1">
                                                @php
                                                    $extension = pathinfo($document, PATHINFO_EXTENSION);
                                                    $filename = basename($document);
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    $isPdf = strtolower($extension) === 'pdf';
                                                    $fileExists = Storage::disk('public')->exists($document);
                                                @endphp
                                                
                                                @if(!$fileExists)
                                                    <div class="text-center text-danger">
                                                        <i class="fas fa-exclamation-triangle fa-3x mb-2"></i>
                                                        <br>
                                                        <small>Fichier introuvable</small>
                                                    </div>
                                                @elseif($isImage)
                                                    <div class="position-relative">
                                                        <img src="{{ Storage::url($document) }}" 
                                                             class="img-thumbnail" 
                                                             style="max-height: 150px; max-width: 100%; object-fit: cover;"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                        <div class="text-center text-danger" style="display: none;">
                                                            <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                                            <br>
                                                            <small class="text-muted">Erreur de chargement</small>
                                                        </div>
                                                    </div>
                                                @elseif($isPdf)
                                                    <div class="text-center">
                                                        <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                                        <br>
                                                        <small class="text-muted font-weight-bold">PDF</small>
                                                    </div>
                                                @else
                                                    <div class="text-center">
                                                        <i class="fas fa-file-alt fa-3x text-muted mb-2"></i>
                                                        <br>
                                                        <small class="text-muted font-weight-bold">{{ strtoupper($extension) }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Nom du fichier -->
                                            <div class="mb-2">
                                                <small class="text-muted d-block" title="{{ $filename }}">
                                                    {{ Str::limit($filename, 25) }}
                                                </small>
                                                @if($fileExists)
                                                    @php
                                                        $fileSize = Storage::disk('public')->size($document);
                                                        $fileSizeFormatted = $fileSize > 1024 * 1024 
                                                            ? round($fileSize / (1024 * 1024), 1) . ' MB'
                                                            : round($fileSize / 1024, 1) . ' KB';
                                                    @endphp
                                                    <small class="text-muted">{{ $fileSizeFormatted }}</small>
                                                @endif
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="mt-auto">
                                                @if($fileExists)
                                                    <a href="{{ route('admin.verifications.download-document', [$verificationRequest, $index]) }}" 
                                                       class="btn btn-sm btn-outline-primary mb-1" target="_blank">
                                                        <i class="fas fa-download"></i> Télécharger
                                                    </a>
                                                    @if($isImage)
                                                        <button type="button" class="btn btn-sm btn-outline-secondary mb-1" 
                                                                onclick="viewImage('{{ Storage::url($document) }}', '{{ $filename }}')">
                                                            <i class="fas fa-eye"></i> Agrandir
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="btn btn-sm btn-outline-danger disabled">
                                                        <i class="fas fa-times"></i> Indisponible
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-times fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Aucun document soumis.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'approbation -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approuver la demande</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.verifications.approve', $verificationRequest) }}" method="POST">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
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
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.verifications.reject', $verificationRequest) }}" method="POST">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Rejeter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de visualisation d'image -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aperçu du document</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function approveRequest() {
    $('#approveModal').modal('show');
}

function rejectRequest() {
    $('#rejectModal').modal('show');
}

function revokeVerification() {
    if (confirm('Êtes-vous sûr de vouloir révoquer la vérification de ce prestataire ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.verifications.revoke", $verificationRequest->prestataire) }}';
        
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

function viewImage(imageUrl, filename = '') {
    // Créer une modal pour afficher l'image en grand
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        cursor: pointer;
        padding: 20px;
    `;
    
    // Titre avec nom du fichier
    if (filename) {
        const title = document.createElement('div');
        title.textContent = filename;
        title.style.cssText = `
            color: white;
            font-size: 16px;
            margin-bottom: 15px;
            text-align: center;
            max-width: 90%;
            word-break: break-word;
        `;
        modal.appendChild(title);
    }
    
    const img = document.createElement('img');
    img.src = imageUrl;
    img.style.cssText = `
        max-width: 90%;
        max-height: 80%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    `;
    
    // Instructions de fermeture
    const instructions = document.createElement('div');
    instructions.textContent = 'Cliquez pour fermer';
    instructions.style.cssText = `
        color: rgba(255,255,255,0.7);
        font-size: 14px;
        margin-top: 15px;
        text-align: center;
    `;
    
    modal.appendChild(img);
    modal.appendChild(instructions);
    document.body.appendChild(modal);
    
    // Fermer la modal en cliquant dessus ou avec Escape
    modal.onclick = function() {
        document.body.removeChild(modal);
    };
    
    document.addEventListener('keydown', function closeOnEscape(e) {
        if (e.key === 'Escape') {
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
            document.removeEventListener('keydown', closeOnEscape);
        }
    });
}
</script>
@endpush
@endsection
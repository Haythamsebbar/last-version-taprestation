@extends('layouts.admin-modern')

@section('title', 'Gestion des Messages')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestion des Messages</h1>
            <p class="mb-0 text-muted">Modérez et gérez tous les messages de la plateforme</p>
        </div>
        <div>
            <a href="{{ route('administrateur.messages.export', request()->query()) }}" class="btn btn-success me-2">
                <i class="fas fa-download"></i> Exporter
            </a>
            <a href="{{ route('administrateur.messages.analytics') }}" class="btn btn-info">
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Non Lus</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['unread']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Signalés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['reported']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['today']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Taux Lecture</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['read_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Temps Réponse</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['avg_response_time'] }}h</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Actions Rapides</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <button type="button" class="btn btn-warning btn-block" onclick="bulkModerate('pending')">
                        <i class="fas fa-clock"></i> Marquer en Attente
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-success btn-block" onclick="bulkModerate('approved')">
                        <i class="fas fa-check"></i> Approuver Sélectionnés
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger btn-block" onclick="bulkModerate('hidden')">
                        <i class="fas fa-eye-slash"></i> Masquer Sélectionnés
                    </button>
                </div>
                <div class="col-md-3">
                    <form method="POST" action="{{ route('administrateur.messages.cleanup') }}" onsubmit="return confirm('Supprimer tous les messages de plus de 6 mois ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary btn-block">
                            <i class="fas fa-broom"></i> Nettoyer Anciens
                        </button>
                    </form>
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
            <form method="GET" action="{{ route('administrateur.messages.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Tous les types</option>
                                <option value="text" {{ request('type') == 'text' ? 'selected' : '' }}>Texte</option>
                                <option value="file" {{ request('type') == 'file' ? 'selected' : '' }}>Fichier</option>
                                <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Image</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Système</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Masqué</option>
                                <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Supprimé</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="read_status">Lecture</label>
                            <select name="read_status" id="read_status" class="form-control">
                                <option value="">Tous</option>
                                <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Lus</option>
                                <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>Non lus</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="reported">Signalement</label>
                            <select name="reported" id="reported" class="form-control">
                                <option value="">Tous</option>
                                <option value="yes" {{ request('reported') == 'yes' ? 'selected' : '' }}>Signalés</option>
                                <option value="no" {{ request('reported') == 'no' ? 'selected' : '' }}>Non signalés</option>
                            </select>
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
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sender">Expéditeur</label>
                            <input type="text" name="sender" id="sender" class="form-control" value="{{ request('sender') }}" placeholder="Nom ou email...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="recipient">Destinataire</label>
                            <input type="text" name="recipient" id="recipient" class="form-control" value="{{ request('recipient') }}" placeholder="Nom ou email...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Recherche</label>
                            <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Contenu du message...">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                            <a href="{{ route('administrateur.messages.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des messages -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Liste des Messages ({{ $messages->total() }} résultats)</h6>
                <div>
                    <input type="checkbox" id="selectAll" class="mr-2"> Tout sélectionner
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($messages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="3%">
                                    <input type="checkbox" id="selectAllTable">
                                </th>
                                <th>Conversation</th>
                                <th>Expéditeur</th>
                                <th>Destinataire</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                <tr class="{{ $message->is_read ? '' : 'table-warning' }} {{ $message->is_reported ? 'table-danger' : '' }}">
                                    <td>
                                        <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" class="message-checkbox">
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">#{{ $message->conversation_id ?? 'N/A' }}</div>
                                        @if($message->client_request)
                                            <div class="text-muted small">{{ Str::limit($message->client_request->title, 30) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                @if($message->sender && $message->sender->profile_photo)
                                                    <img src="{{ asset('storage/' . $message->sender->profile_photo) }}" alt="Photo" class="rounded-circle" width="30" height="30">
                                                @else
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $message->sender->name ?? 'Utilisateur supprimé' }}</div>
                                                <div class="text-muted small">{{ $message->sender->email ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                @if($message->recipient && $message->recipient->profile_photo)
                                                    <img src="{{ asset('storage/' . $message->recipient->profile_photo) }}" alt="Photo" class="rounded-circle" width="30" height="30">
                                                @else
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $message->recipient->name ?? 'Utilisateur supprimé' }}</div>
                                                <div class="text-muted small">{{ $message->recipient->email ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="message-content">
                                            @if($message->type === 'text')
                                                <div>{{ Str::limit($message->content, 60) }}</div>
                                            @elseif($message->type === 'file')
                                                <div class="text-info">
                                                    <i class="fas fa-file"></i> Fichier: {{ $message->file_name ?? 'fichier.ext' }}
                                                </div>
                                            @elseif($message->type === 'image')
                                                <div class="text-success">
                                                    <i class="fas fa-image"></i> Image: {{ $message->file_name ?? 'image.jpg' }}
                                                </div>
                                            @else
                                                <div class="text-muted">
                                                    <i class="fas fa-cog"></i> Message système
                                                </div>
                                            @endif
                                            @if($message->is_reported)
                                                <div class="text-danger small mt-1">
                                                    <i class="fas fa-flag"></i> Signalé
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @switch($message->type)
                                            @case('text')
                                                <span class="badge badge-primary"><i class="fas fa-comment"></i> Texte</span>
                                                @break
                                            @case('file')
                                                <span class="badge badge-info"><i class="fas fa-file"></i> Fichier</span>
                                                @break
                                            @case('image')
                                                <span class="badge badge-success"><i class="fas fa-image"></i> Image</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary"><i class="fas fa-cog"></i> Système</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($message->moderation_status)
                                            @case('pending')
                                                <span class="badge badge-warning">En attente</span>
                                                @break
                                            @case('approved')
                                                <span class="badge badge-success">Approuvé</span>
                                                @break
                                            @case('hidden')
                                                <span class="badge badge-danger">Masqué</span>
                                                @break
                                            @case('deleted')
                                                <span class="badge badge-dark">Supprimé</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($message->moderation_status ?? 'N/A') }}</span>
                                        @endswitch
                                        
                                        @if(!$message->is_read)
                                            <div class="text-warning small mt-1">
                                                <i class="fas fa-exclamation"></i> Non lu
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $message->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $message->created_at->format('H:i') }}</div>
                                        <div class="text-muted small">{{ $message->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical" role="group">
                                            <a href="{{ route('administrateur.messages.show', $message->id) }}" class="btn btn-sm btn-info mb-1" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($message->moderation_status !== 'approved')
                                                <form method="POST" action="{{ route('administrateur.messages.moderate', $message->id) }}" style="display: inline;" class="mb-1">
                                                    @csrf
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approuver">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($message->moderation_status !== 'hidden')
                                                <form method="POST" action="{{ route('administrateur.messages.moderate', $message->id) }}" style="display: inline;" class="mb-1">
                                                    @csrf
                                                    <input type="hidden" name="action" value="hide">
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Masquer">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $message->id }}')" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Actions en masse -->
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" onclick="bulkModerate('approved')">
                                    <i class="fas fa-check"></i> Approuver
                                </button>
                                <button type="button" class="btn btn-warning" onclick="bulkModerate('hidden')">
                                    <i class="fas fa-eye-slash"></i> Masquer
                                </button>
                                <button type="button" class="btn btn-info" onclick="bulkMarkAsRead()">
                                    <i class="fas fa-eye"></i> Marquer comme lus
                                </button>
                                <button type="button" class="btn btn-danger" onclick="bulkDelete()">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <span id="selectedCount">0</span> message(s) sélectionné(s)
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Affichage de {{ $messages->firstItem() }} à {{ $messages->lastItem() }} sur {{ $messages->total() }} résultats
                    </div>
                    <div>
                        {{ $messages->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-envelope-open fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Aucun message trouvé</h5>
                    <p class="text-muted">Aucun message ne correspond aux critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de suppression -->
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
                <p>Êtes-vous sûr de vouloir supprimer ce message ? Cette action est irréversible.</p>
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
// Gestion de la sélection multiple
$(document).ready(function() {
    // Toggle all checkboxes
    $('#selectAll, #selectAllTable').change(function() {
        $('.message-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });
    
    // Update count when individual checkbox changes
    $('.message-checkbox').change(function() {
        updateSelectedCount();
    });
    
    // Auto-submit form on filter change
    $('#type, #status, #read_status, #reported').change(function() {
        $(this).closest('form').submit();
    });
});

function updateSelectedCount() {
    const count = $('.message-checkbox:checked').length;
    $('#selectedCount').text(count);
    
    // Update select all checkbox state
    const total = $('.message-checkbox').length;
    $('#selectAll, #selectAllTable').prop('indeterminate', count > 0 && count < total);
    $('#selectAll, #selectAllTable').prop('checked', count === total && total > 0);
}

function confirmDelete(messageId) {
    const form = document.getElementById('deleteForm');
    form.action = `/administrateur/messages/${messageId}`;
    $('#deleteModal').modal('show');
}

function bulkModerate(action) {
    const selected = $('.message-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selected.length === 0) {
        alert('Veuillez sélectionner au moins un message.');
        return;
    }
    
    const actionText = {
        'approved': 'approuver',
        'hidden': 'masquer',
        'pending': 'marquer en attente'
    };
    
    if (confirm(`${actionText[action]} ${selected.length} message(s) ?`)) {
        submitBulkAction('{{ route("administrateur.messages.bulk-moderate") }}', selected, { action: action });
    }
}

function bulkMarkAsRead() {
    const selected = $('.message-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selected.length === 0) {
        alert('Veuillez sélectionner au moins un message.');
        return;
    }
    
    if (confirm(`Marquer ${selected.length} message(s) comme lu(s) ?`)) {
        submitBulkAction('{{ route("administrateur.messages.bulk-mark-read") }}', selected);
    }
}

function bulkDelete() {
    const selected = $('.message-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selected.length === 0) {
        alert('Veuillez sélectionner au moins un message.');
        return;
    }
    
    if (confirm(`Supprimer définitivement ${selected.length} message(s) ?`)) {
        submitBulkAction('{{ route("administrateur.messages.bulk-delete") }}', selected, {}, 'DELETE');
    }
}

function submitBulkAction(url, messageIds, extraData = {}, method = 'POST') {
    const form = $('<form>', {
        method: 'POST',
        action: url
    });
    
    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: '{{ csrf_token() }}'
    }));
    
    if (method === 'DELETE') {
        form.append($('<input>', {
            type: 'hidden',
            name: '_method',
            value: 'DELETE'
        }));
    }
    
    messageIds.forEach(id => {
        form.append($('<input>', {
            type: 'hidden',
            name: 'message_ids[]',
            value: id
        }));
    });
    
    Object.keys(extraData).forEach(key => {
        form.append($('<input>', {
            type: 'hidden',
            name: key,
            value: extraData[key]
        }));
    });
    
    $('body').append(form);
    form.submit();
}
</script>
@endpush
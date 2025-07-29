@extends('layouts.admin-modern')

@section('title', 'Gestion des Notifications')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestion des Notifications</h1>
            <p class="mb-0 text-muted">Gérez toutes les notifications du système</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary me-2" data-toggle="modal" data-target="#sendNotificationModal">
                <i class="fas fa-paper-plane"></i> Envoyer Notification
            </button>
            <a href="{{ route('administrateur.notifications.analytics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Analyses
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Notifications</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Non Lues</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['unread']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
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

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Taux de Lecture</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['read_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                    <form method="POST" action="{{ route('administrateur.notifications.mark-all-read') }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check-double"></i> Marquer Toutes comme Lues
                        </button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form method="POST" action="{{ route('administrateur.notifications.cleanup') }}" onsubmit="return confirm('Supprimer toutes les notifications de plus de 30 jours ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-broom"></i> Nettoyer Anciennes
                        </button>
                    </form>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#bulkDeleteModal">
                        <i class="fas fa-trash-alt"></i> Suppression en Masse
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('administrateur.notifications.export', request()->query()) }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-download"></i> Exporter
                    </a>
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
            <form method="GET" action="{{ route('administrateur.notifications.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Tous les types</option>
                                <option value="booking" {{ request('type') == 'booking' ? 'selected' : '' }}>Réservation</option>
                                <option value="offer" {{ request('type') == 'offer' ? 'selected' : '' }}>Offre</option>
                                <option value="message" {{ request('type') == 'message' ? 'selected' : '' }}>Message</option>
                                <option value="review" {{ request('type') == 'review' ? 'selected' : '' }}>Avis</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Système</option>
                                <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Paiement</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="read_status">Statut de Lecture</label>
                            <select name="read_status" id="read_status" class="form-control">
                                <option value="">Tous</option>
                                <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Lues</option>
                                <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>Non lues</option>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Recherche</label>
                            <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Contenu, utilisateur...">
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des notifications -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Notifications ({{ $notifications->total() }} résultats)</h6>
        </div>
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Utilisateur</th>
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Contenu</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                                <tr class="{{ $notification->read_at ? '' : 'table-warning' }}">
                                    <td>
                                        <input type="checkbox" name="notification_ids[]" value="{{ $notification->id }}" class="notification-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                @if($notification->notifiable && $notification->notifiable->profile_photo)
                                                    <img src="{{ asset('storage/' . $notification->notifiable->profile_photo) }}" alt="Photo" class="rounded-circle" width="30" height="30">
                                                @else
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $notification->notifiable->name ?? 'Utilisateur supprimé' }}</div>
                                                <div class="text-muted small">{{ $notification->notifiable->email ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'booking' => 'primary',
                                                'offer' => 'success',
                                                'message' => 'info',
                                                'review' => 'warning',
                                                'system' => 'secondary',
                                                'payment' => 'danger'
                                            ];
                                            $typeIcons = [
                                                'booking' => 'calendar-check',
                                                'offer' => 'handshake',
                                                'message' => 'envelope',
                                                'review' => 'star',
                                                'system' => 'cog',
                                                'payment' => 'credit-card'
                                            ];
                                            $data = $notification->data;
                                            $type = $data['type'] ?? 'system';
                                        @endphp
                                        <span class="badge badge-{{ $typeColors[$type] ?? 'secondary' }}">
                                            <i class="fas fa-{{ $typeIcons[$type] ?? 'bell' }}"></i>
                                            {{ ucfirst($type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ Str::limit($data['title'] ?? 'Notification', 40) }}</div>
                                    </td>
                                    <td>
                                        <div class="text-muted">{{ Str::limit($data['message'] ?? 'Aucun contenu', 60) }}</div>
                                    </td>
                                    <td>
                                        @if($notification->read_at)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Lue
                                            </span>
                                            <div class="text-muted small">{{ $notification->read_at->format('d/m/Y H:i') }}</div>
                                        @else
                                            <span class="badge badge-warning">
                                                <i class="fas fa-exclamation"></i> Non lue
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $notification->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $notification->created_at->format('H:i') }}</div>
                                        <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administrateur.notifications.show', $notification->id) }}" class="btn btn-sm btn-info" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$notification->read_at)
                                                <form method="POST" action="{{ route('administrateur.notifications.mark-read', $notification->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Marquer comme lue">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $notification->id }}')" title="Supprimer">
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
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" onclick="markSelectedAsRead()">
                                    <i class="fas fa-check"></i> Marquer sélectionnées comme lues
                                </button>
                                <button type="button" class="btn btn-danger" onclick="deleteSelected()">
                                    <i class="fas fa-trash"></i> Supprimer sélectionnées
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <span id="selectedCount">0</span> notification(s) sélectionnée(s)
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Affichage de {{ $notifications->firstItem() }} à {{ $notifications->lastItem() }} sur {{ $notifications->total() }} résultats
                    </div>
                    <div>
                        {{ $notifications->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-bell-slash fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Aucune notification trouvée</h5>
                    <p class="text-muted">Aucune notification ne correspond aux critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'envoi de notification -->
<div class="modal fade" id="sendNotificationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Envoyer une Notification</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('administrateur.notifications.send') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient_type">Type de Destinataire</label>
                        <select name="recipient_type" id="recipient_type" class="form-control" required>
                            <option value="all">Tous les utilisateurs</option>
                            <option value="clients">Tous les clients</option>
                            <option value="prestataires">Tous les prestataires</option>
                            <option value="specific">Utilisateurs spécifiques</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="specific_users_group" style="display: none;">
                        <label for="user_ids">Utilisateurs Spécifiques</label>
                        <select name="user_ids[]" id="user_ids" class="form-control" multiple>
                            @foreach(\App\Models\User::select('id', 'name', 'email')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Maintenez Ctrl/Cmd pour sélectionner plusieurs utilisateurs</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="notification_type">Type de Notification</label>
                        <select name="type" id="notification_type" class="form-control" required>
                            <option value="system">Système</option>
                            <option value="announcement">Annonce</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="promotion">Promotion</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title" class="form-control" required maxlength="255">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="action_url">URL d'Action (optionnel)</label>
                        <input type="url" name="action_url" id="action_url" class="form-control" placeholder="https://...">
                        <small class="form-text text-muted">URL vers laquelle rediriger l'utilisateur en cliquant sur la notification</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
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
                <p>Êtes-vous sûr de vouloir supprimer cette notification ? Cette action est irréversible.</p>
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
    $('#selectAll').change(function() {
        $('.notification-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });
    
    // Update count when individual checkbox changes
    $('.notification-checkbox').change(function() {
        updateSelectedCount();
    });
    
    // Show/hide specific users field
    $('#recipient_type').change(function() {
        if ($(this).val() === 'specific') {
            $('#specific_users_group').show();
            $('#user_ids').prop('required', true);
        } else {
            $('#specific_users_group').hide();
            $('#user_ids').prop('required', false);
        }
    });
    
    // Auto-submit form on filter change
    $('#type, #read_status').change(function() {
        $(this).closest('form').submit();
    });
});

function updateSelectedCount() {
    const count = $('.notification-checkbox:checked').length;
    $('#selectedCount').text(count);
    
    // Update select all checkbox state
    const total = $('.notification-checkbox').length;
    $('#selectAll').prop('indeterminate', count > 0 && count < total);
    $('#selectAll').prop('checked', count === total && total > 0);
}

function confirmDelete(notificationId) {
    const form = document.getElementById('deleteForm');
    form.action = `/administrateur/notifications/${notificationId}`;
    $('#deleteModal').modal('show');
}

function markSelectedAsRead() {
    const selected = $('.notification-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selected.length === 0) {
        alert('Veuillez sélectionner au moins une notification.');
        return;
    }
    
    if (confirm(`Marquer ${selected.length} notification(s) comme lue(s) ?`)) {
        // Create form and submit
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("administrateur.notifications.mark-selected-read") }}'
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        selected.forEach(id => {
            form.append($('<input>', {
                type: 'hidden',
                name: 'notification_ids[]',
                value: id
            }));
        });
        
        $('body').append(form);
        form.submit();
    }
}

function deleteSelected() {
    const selected = $('.notification-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selected.length === 0) {
        alert('Veuillez sélectionner au moins une notification.');
        return;
    }
    
    if (confirm(`Supprimer définitivement ${selected.length} notification(s) ?`)) {
        // Create form and submit
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("administrateur.notifications.bulk-delete") }}'
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_method',
            value: 'DELETE'
        }));
        
        selected.forEach(id => {
            form.append($('<input>', {
                type: 'hidden',
                name: 'notification_ids[]',
                value: id
            }));
        });
        
        $('body').append(form);
        form.submit();
    }
}
</script>
@endpush
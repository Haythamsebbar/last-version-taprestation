@extends('layouts.admin-modern')

@section('page-title', 'Gestion des Clients')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Header Actions -->
<div class="page-header">
    <div class="page-header-content">
        <h2 class="page-title">Clients</h2>
        <p class="page-subtitle">Gérez tous les clients de la plateforme</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-outline" onclick="toggleFilters()">
            <i class="fas fa-filter"></i>
            <span class="btn-text">Filtres</span>
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card primary">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Clients</div>
                <div class="stat-value">{{ $clients->total() ?? 0 }}</div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-title">Clients Actifs</div>
                <div class="stat-value">{{ $clients->where('blocked_at', null)->count() ?? 0 }}</div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-title">Demandes Totales</div>
                <div class="stat-value">{{ $totalRequests ?? 0 }}</div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-header">
            <div>
                <div class="stat-title">Nouveaux ce mois</div>
                <div class="stat-value">{{ $clients->where('created_at', '>=', now()->startOfMonth())->count() ?? 0 }}</div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters Panel -->
<div id="filtersPanel" class="chart-card" style="display: none; margin-bottom: 2rem;">
    <div class="chart-header">
        <div class="chart-title">Filtres de recherche</div>
        <button class="btn btn-outline" onclick="clearFilters()">
            <i class="fas fa-times"></i>
            Effacer
        </button>
    </div>
    <form action="{{ route('administrateur.clients.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; padding: 1rem 0;">
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Nom</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Rechercher par nom..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Email</label>
            <input type="email" name="email" value="{{ request('email') }}" placeholder="Rechercher par email..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Statut</label>
            <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Bloqué</option>
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Trier par</label>
            <select name="sort" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date d'inscription</option>
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom</option>
                <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                <option value="requests_count" {{ request('sort') == 'requests_count' ? 'selected' : '' }}>Nombre de demandes</option>
                <option value="reviews_count" {{ request('sort') == 'reviews_count' ? 'selected' : '' }}>Nombre d'avis</option>
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Ordre</label>
            <select name="direction" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Croissant</option>
            </select>
        </div>
        
        <div style="grid-column: 1 / -1; display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Rechercher
            </button>
            <a href="{{ route('administrateur.clients.index') }}" class="btn btn-outline">
                <i class="fas fa-redo"></i>
                Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- Items Per Page & Export -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <div>
        <label style="font-size: 0.875rem; color: var(--secondary);">Afficher</label>
        <select onchange="changeItemsPerPage(this.value)" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
        </select>
        <span style="font-size: 0.875rem; color: var(--secondary);">éléments</span>
    </div>
    
    <button class="btn btn-outline" onclick="exportClients()">
        <i class="fas fa-download"></i>
        Exporter
    </button>
</div>

<!-- Main Content -->
<div class="content-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
                    </th>
                    <th>Client</th>
                    <th>Demandes</th>
                    <th>Avis</th>
                    <th>Statut</th>
                    <th>Date d'inscription</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>
                        <input type="checkbox" class="client-checkbox" value="{{ $client->id }}" onchange="updateBulkActionsVisibility()">
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="avatar">
                                @if($client->user->profile_photo_url)
                                    <img src="{{ $client->user->profile_photo_url }}" alt="{{ $client->user->name }}">
                                @else
                                    <div class="avatar-initials">
                                        {{ substr($client->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--dark);">{{ $client->user->name }}</div>
                                <div style="font-size: 0.875rem; color: var(--secondary);">{{ $client->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge primary">{{ $client->client_requests_count ?? $client->clientRequests->count() }}</span>
                    </td>
                    <td>
                        <span class="badge info">{{ $client->reviews_count ?? $client->reviews->count() }}</span>
                    </td>
                    <td>
                        @if($client->user->is_blocked)
            <span class="badge danger">Bloqué</span>
        @else
            <span class="badge success">Actif</span>
        @endif
                    </td>
                    <td>{{ $client->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="actions-dropdown">
                            <button class="btn btn-icon" onclick="toggleDropdown('clientMenu{{ $client->id }}')">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="clientMenu{{ $client->id }}" class="dropdown-menu" style="display: none;">
                                <a href="{{ route('administrateur.clients.show', $client->id) }}" class="dropdown-item">
                                    <i class="fas fa-eye"></i> Voir détails
                                </a>
                                @if(auth()->id() != $client->user_id)
                                    <button onclick="toggleBlockClient('{{ $client->id }}', '{{ $client->user->is_blocked ? 'unblock' : 'block' }}')" class="dropdown-item">
                                        <i class="fas {{ $client->user->is_blocked ? 'fa-unlock' : 'fa-lock' }}"></i> 
                                        {{ $client->user->is_blocked ? 'Débloquer' : 'Bloquer' }}
                                    </button>
                                    <button onclick="deleteClient('{{ $client->id }}')" class="dropdown-item text-danger">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div style="color: var(--secondary); font-size: 1rem;">
                            <i class="fas fa-info-circle mr-1"></i> Aucun client trouvé
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div style="padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 0.875rem; color: var(--secondary);">
            Affichage de {{ $clients->firstItem() ?? 0 }} à {{ $clients->lastItem() ?? 0 }} sur {{ $clients->total() }} entrées
        </div>
        {{ $clients->appends(request()->query())->links() }}
    </div>
</div>

<!-- Bulk Actions -->
<div id="bulkActions" style="position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: white; padding: 1rem 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); display: none; z-index: 1000;">
    <div style="display: flex; gap: 1rem; align-items: center;">
        <span id="selectedCount" style="font-weight: 500;">0 sélectionné(s)</span>
        <button class="btn btn-outline" onclick="clearSelection()">
            <i class="fas fa-times"></i>
            Annuler
        </button>
        <button class="btn btn-success" onclick="bulkUnblock()">
            <i class="fas fa-unlock"></i>
            Débloquer
        </button>
        <button class="btn btn-warning" onclick="bulkBlock()">
            <i class="fas fa-lock"></i>
            Bloquer
        </button>
        <button class="btn btn-danger" onclick="bulkDelete()">
            <i class="fas fa-trash"></i>
            Supprimer
        </button>
    </div>
</div>

<script>
    // Toggle filters panel
    function toggleFilters() {
        const filtersPanel = document.getElementById('filtersPanel');
        filtersPanel.style.display = filtersPanel.style.display === 'none' ? 'block' : 'none';
    }
    
    // Clear filters
    function clearFilters() {
        window.location.href = '{{ route("administrateur.clients.index") }}';
    }
    
    // Change items per page
    function changeItemsPerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        window.location.href = url.toString();
    }
    
    // Toggle dropdown menu
    function toggleDropdown(menuId) {
        const menu = document.getElementById(menuId);
        const allMenus = document.querySelectorAll('.dropdown-menu');
        
        // Close all other menus
        allMenus.forEach(item => {
            if (item.id !== menuId) {
                item.style.display = 'none';
            }
        });
        
        // Toggle current menu
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
    
    // Toggle all checkboxes
    function toggleAllCheckboxes() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.client-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        
        updateBulkActionsVisibility();
    }
    
    // Update bulk actions visibility
    function updateBulkActionsVisibility() {
        const checkboxes = document.querySelectorAll('.client-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        
        if (checkboxes.length > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = `${checkboxes.length} sélectionné(s)`;
        } else {
            bulkActions.style.display = 'none';
        }
    }
    
    // Clear selection
    function clearSelection() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.client-checkbox');
        
        selectAll.checked = false;
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        updateBulkActionsVisibility();
    }
    
    // Toggle block client
    function toggleBlockClient(clientId, action) {
        const message = action === 'block' ? 'Êtes-vous sûr de vouloir bloquer ce client ?' : 'Êtes-vous sûr de vouloir débloquer ce client ?';
        
        if (confirm(message)) {
            fetch(`{{ url('/administrateur/clients') }}/${clientId}/toggle-block`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Une erreur est survenue', 'error');
            });
        }
    }
    
    // Delete client
    function deleteClient(clientId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.')) {
            fetch(`{{ url('/administrateur/clients') }}/${clientId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Une erreur est survenue', 'error');
            });
        }
    }
    
    // Bulk unblock
    function bulkUnblock() {
        const selectedIds = getSelectedIds();
        
        if (confirm(`Êtes-vous sûr de vouloir débloquer ${selectedIds.length} client(s) ?`)) {
            fetch('{{ route("administrateur.clients.bulk-unblock") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Une erreur est survenue', 'error');
            });
        }
    }
    
    // Bulk block
    function bulkBlock() {
        const selectedIds = getSelectedIds();
        
        if (confirm(`Êtes-vous sûr de vouloir bloquer ${selectedIds.length} client(s) ?`)) {
            fetch('{{ route("administrateur.clients.bulk-block") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Une erreur est survenue', 'error');
            });
        }
    }
    
    // Bulk delete
    function bulkDelete() {
        const selectedIds = getSelectedIds();
        
        if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedIds.length} client(s) ? Cette action est irréversible.`)) {
            fetch('{{ route("administrateur.clients.bulk-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Une erreur est survenue', 'error');
            });
        }
    }
    
    // Get selected IDs
    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.client-checkbox:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.value);
    }
    
    // Export clients
    function exportClients() {
        window.location.href = '{{ route("administrateur.clients.export") }}' + window.location.search;
    }

    // Show notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => document.body.removeChild(notification), 300);
        }, 3000);
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.actions-dropdown')) {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    });
</script>

<style>
/* Page Header Styles */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-header-content {
    flex: 1;
    min-width: 200px;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
}

.page-subtitle {
    color: var(--secondary);
    margin: 0.5rem 0 0 0;
    font-size: 0.9rem;
}

.page-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Button responsive text */
.btn-text {
    margin-left: 0.5rem;
}

/* Notification Styles */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 1rem;
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    max-width: 400px;
    border-left: 4px solid;
}

.notification.show {
    transform: translateX(0);
}

.notification-success {
    border-left-color: var(--success);
}

.notification-error {
    border-left-color: var(--danger);
}

.notification-info {
    border-left-color: var(--primary);
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.notification-content i {
    font-size: 1.1rem;
}

.notification-success .notification-content i {
    color: var(--success);
}

.notification-error .notification-content i {
    color: var(--danger);
}

.notification-info .notification-content i {
    color: var(--primary);
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .page-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .btn-text {
        display: none;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
    }
    
    .filter-panel {
        padding: 1rem !important;
    }
    
    .filter-grid {
        grid-template-columns: 1fr !important;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .table th,
    .table td {
        white-space: nowrap;
        min-width: 120px;
    }
    
    .table th:first-child,
    .table td:first-child {
        position: sticky;
        left: 0;
        background: white;
        z-index: 1;
    }
    
    .bulk-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .bulk-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .notification {
        right: 10px;
        left: 10px;
        max-width: none;
        transform: translateY(-100%);
    }
    
    .notification.show {
        transform: translateY(0);
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr !important;
    }
    
    .page-title {
        font-size: 1.25rem;
    }
    
    .table th,
    .table td {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
}

/* Enhanced table responsiveness */
@media (max-width: 992px) {
    .table-responsive {
        border: none;
    }
    
    .table {
        font-size: 0.9rem;
    }
    
    .dropdown-menu {
        position: fixed !important;
        transform: none !important;
        left: auto !important;
        right: 10px !important;
    }
}
</style>
@endsection
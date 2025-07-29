@extends('layouts.admin-modern')

@section('title', 'Gestion des Utilisateurs')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<!-- Header Actions -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--dark); margin: 0;">Utilisateurs</h2>
        <p style="color: var(--secondary); margin: 0.5rem 0 0 0;">Gérez tous les utilisateurs de la plateforme</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <button class="btn btn-outline" onclick="toggleFilters()">
            <i class="fas fa-filter"></i>
            Filtres
        </button>
        <a href="{{ route('administrateur.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nouvel utilisateur
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card primary">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Utilisateurs</div>
                <div class="stat-value">{{ $users->total() ?? 0 }}</div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-title">Utilisateurs Actifs</div>
                <div class="stat-value">{{ $users->where('is_blocked', false)->count() ?? 0 }}</div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-title">Administrateurs</div>
                <div class="stat-value">{{ $users->where('role', 'administrateur')->count() ?? 0 }}</div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-header">
            <div>
                <div class="stat-title">Nouveaux ce mois</div>
                <div class="stat-value">{{ $users->where('created_at', '>=', now()->startOfMonth())->count() ?? 0 }}</div>
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
    <form action="{{ route('administrateur.users.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; padding: 1rem 0;">
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Nom</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Rechercher par nom..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Email</label>
            <input type="email" name="email" value="{{ request('email') }}" placeholder="Rechercher par email..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Rôle</label>
            <select name="role" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Tous les rôles</option>
                <option value="administrateur" {{ request('role') == 'administrateur' ? 'selected' : '' }}>Administrateur</option>
                <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Client</option>
                <option value="prestataire" {{ request('role') == 'prestataire' ? 'selected' : '' }}>Prestataire</option>
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Statut</label>
            <select name="is_blocked" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Tous</option>
                <option value="0" {{ request('is_blocked') == '0' ? 'selected' : '' }}>Actif</option>
                <option value="1" {{ request('is_blocked') == '1' ? 'selected' : '' }}>Bloqué</option>
            </select>
        </div>
        
        <div style="display: flex; align-items: end; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Rechercher
            </button>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-title">Liste des utilisateurs ({{ $users->total() ?? 0 }})</div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <select onchange="changePerPage(this.value)" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem;">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 par page</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 par page</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 par page</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 par page</option>
            </select>
            
            <button class="btn btn-outline" onclick="exportUsers()">
                <i class="fas fa-download"></i>
                Exporter
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" onchange="toggleAllCheckboxes(this)" style="margin-right: 0.5rem;">
                        Utilisateur
                    </th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Dernière connexion</th>
                    <th>Inscrit le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users ?? [] as $user)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="user-checkbox">
                                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;
                                    @if($user->role === 'administrateur') background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                                    @elseif($user->role === 'prestataire') background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
                                    @else background: linear-gradient(135deg, var(--info) 0%, #0891b2 100%); @endif">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--dark);">{{ $user->name }}</div>
                                    <div style="font-size: 0.875rem; color: var(--secondary);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500;
                                @if($user->role === 'administrateur') background: rgba(79, 70, 229, 0.1); color: var(--primary);
                                @elseif($user->role === 'prestataire') background: rgba(16, 185, 129, 0.1); color: var(--success);
                                @else background: rgba(6, 182, 212, 0.1); color: var(--info); @endif">
                                <i class="fas 
                                    @if($user->role === 'administrateur') fa-user-shield
                                    @elseif($user->role === 'prestataire') fa-user-tie
                                    @else fa-user @endif" style="margin-right: 0.25rem;"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span style="padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500;
                                @if(!$user->is_blocked) background: rgba(16, 185, 129, 0.1); color: var(--success);
                                @else background: rgba(239, 68, 68, 0.1); color: var(--danger); @endif">
                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.5rem;"></i>
                                {{ !$user->is_blocked ? 'Actif' : 'Bloqué' }}
                            </span>
                        </td>
                        <td style="color: var(--secondary); font-size: 0.875rem;">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Jamais' }}
                        </td>
                        <td style="color: var(--secondary); font-size: 0.875rem;">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('administrateur.users.show', $user->id) }}" class="btn btn-outline" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if(!$user->is_blocked)
                                    <button onclick="toggleBlockUser({{ $user->id }})" class="btn btn-danger" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Bloquer">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <button onclick="toggleBlockUser({{ $user->id }})" class="btn btn-success" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Débloquer">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                
                                <button onclick="deleteUser({{ $user->id }})" class="btn btn-danger" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--secondary);">
                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; color: #e2e8f0;"></i>
                            <div style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">Aucun utilisateur trouvé</div>
                            <div>Essayez de modifier vos critères de recherche</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users && $users->hasPages())
        <div style="padding: 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="color: var(--secondary); font-size: 0.875rem;">
                Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }} sur {{ $users->total() }} résultats
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Bulk Actions -->
<div id="bulkActions" style="position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: white; padding: 1rem 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); display: none; z-index: 1000;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="font-weight: 500;">Actions groupées :</span>
        <button class="btn btn-danger" onclick="bulkDelete()">
            <i class="fas fa-trash"></i>
            Supprimer
        </button>
        <button class="btn btn-warning" onclick="bulkBlock()">
            <i class="fas fa-ban"></i>
            Bloquer
        </button>
        <button class="btn btn-success" onclick="bulkUnblock()">
            <i class="fas fa-check"></i>
            Débloquer
        </button>
        <button class="btn btn-outline" onclick="clearSelection()">
            <i class="fas fa-times"></i>
            Annuler
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table th, .table td {
            vertical-align: middle;
            padding: 12px;
            white-space: nowrap;
        }
        
        .badge {
            font-size: 0.875rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .pagination {
            margin: 0;
        }
        
        .pagination .page-link {
            color: #6c757d;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 1rem !important;
            }
            
            .filters-section {
                flex-direction: column !important;
                gap: 1rem !important;
            }
            
            .filters-section .d-flex {
                flex-direction: column !important;
                gap: 0.5rem !important;
            }
            
            .table th, .table td {
                padding: 8px 4px;
                font-size: 0.875rem;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn-group .btn {
                border-radius: 0.375rem !important;
                margin-bottom: 2px;
            }
            
            .bulk-actions {
                flex-direction: column !important;
                gap: 0.5rem !important;
            }
            
            .bulk-actions .btn {
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr !important;
            }
            
            .table-responsive {
                font-size: 0.8rem;
            }
            
            .card-header h5 {
                font-size: 1.1rem;
            }
        }
</style>
@endpush

@push('scripts')
<script>
// Toggle filters panel
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

// Clear filters
function clearFilters() {
    window.location.href = '{{ route("administrateur.users.index") }}';
}

// Change items per page
function changePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    window.location.href = url.toString();
}

// Toggle all checkboxes
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
    updateBulkActions();
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
}

// Add event listeners to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Clear selection
function clearSelection() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
}

// User actions
function toggleBlockUser(userId) {
    const action = event.target.closest('button').title === 'Bloquer' ? 'bloquer' : 'débloquer';
    if (confirm(`Êtes-vous sûr de vouloir ${action} cet utilisateur ?`)) {
        fetch(`{{ url('/administrateur/users') }}/${userId}/toggle-block`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'opération');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'opération');
        });
    }
}

function deleteUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
        fetch(`{{ url('/administrateur/users') }}/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    }
}

// Bulk actions
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${checkedBoxes.length} utilisateur(s) ? Cette action est irréversible.`)) {
        const userIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        fetch('{{ route("administrateur.users.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ user_ids: userIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression groupée');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression groupée');
        });
    }
}

function bulkBlock() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir bloquer ${checkedBoxes.length} utilisateur(s) ?`)) {
        const userIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        fetch('{{ route("administrateur.users.bulk-block") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ user_ids: userIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors du blocage groupé');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du blocage groupé');
        });
    }
}

function bulkUnblock() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir débloquer ${checkedBoxes.length} utilisateur(s) ?`)) {
        const userIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        fetch('{{ route("administrateur.users.bulk-unblock") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ user_ids: userIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors du déblocage groupé');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du déblocage groupé');
        });
    }
}

// Export users
function exportUsers() {
    window.location.href = '{{ route("administrateur.users.export") }}';
}
</script>
@endpush
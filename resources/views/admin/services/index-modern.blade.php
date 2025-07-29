@extends('layouts.admin-modern')

@section('page-title', 'Gestion des Services')

@section('content')
<!-- Header Actions -->
<div style="display: flex; justify-content: between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--dark); margin: 0;">Services</h2>
        <p style="color: var(--secondary); margin: 0.5rem 0 0 0;">Gérez tous les services publiés sur la plateforme</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('administrateur.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nouveau Service
        </a>
        <button class="btn btn-outline" onclick="toggleFilters()">
            <i class="fas fa-filter"></i>
            Filtres
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card primary">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Services</div>
                <div class="stat-value">{{ $services->total() ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8% ce mois</span>
                </div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-briefcase"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-title">Services Actifs</div>
                <div class="stat-value">{{ $services->where('status', 'active')->count() ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12% ce mois</span>
                </div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-title">En Attente de Validation</div>
                <div class="stat-value">{{ $services->where('status', 'pending')->count() ?? 0 }}</div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down"></i>
                    <span>-3% ce mois</span>
                </div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-header">
            <div>
                <div class="stat-title">Revenus Moyens</div>
                <div class="stat-value"><!-- Prix moyen supprimé pour confidentialité --></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+5% ce mois</span>
                </div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-euro-sign"></i>
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
    <form action="{{ route('administrateur.services.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; padding: 1rem 0;">
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Titre du service</label>
            <input type="text" name="title" value="{{ request('title') }}" placeholder="Rechercher par titre..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Prestataire</label>
            <input type="text" name="prestataire" value="{{ request('prestataire') }}" placeholder="Nom du prestataire..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Catégorie</label>
            <select name="category" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Toutes les catégories</option>
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Statut</label>
            <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Tous</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Prix</label>
            <div style="display: flex; gap: 0.5rem;">
                <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Min" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Max" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
            </div>
        </div>
        
        <div style="display: flex; align-items: end; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Rechercher
            </button>
        </div>
    </form>
</div>

<!-- Services Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-title">Liste des services ({{ $services->total() ?? 0 }})</div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <select onchange="changePerPage(this.value)" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem;">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 par page</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 par page</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 par page</option>
            </select>
            
            <button class="btn btn-outline" onclick="exportServices()">
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
                        Service
                    </th>
                    <th>Prestataire</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Note</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services ?? [] as $service)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <input type="checkbox" name="selected_services[]" value="{{ $service->id }}" class="service-checkbox">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    @if($service->image)
                                        <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover;">
                                    @else
                                        <div style="width: 48px; height: 48px; border-radius: 8px; background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.1rem;">
                                            {{ substr($service->title, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 600; color: var(--dark); margin-bottom: 0.25rem;">{{ Str::limit($service->title, 40) }}</div>
                                        <div style="font-size: 0.875rem; color: var(--secondary);">{{ Str::limit($service->description, 60) }}</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--info) 0%, #06b6d4 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                    {{ substr($service->prestataire->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--dark); font-size: 0.875rem;">{{ $service->prestataire->user->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--secondary);">{{ $service->prestataire->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($service->categories->first())
                                <span style="padding: 0.375rem 0.75rem; background: rgba(139, 69, 19, 0.1); color: #8b4513; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                    {{ $service->categories->first()->name }}
                                </span>
                            @else
                                <span style="color: var(--secondary); font-style: italic;">Non catégorisé</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--dark); font-size: 1rem;">
                                €{{ number_format($service->price, 2) }}
                            </div>
                            @if($service->price_type)
                                <div style="font-size: 0.8rem; color: var(--secondary);">{{ $service->price_type }}</div>
                            @endif
                        </td>
                        <td>
                            @switch($service->status)
                                @case('active')
                                    <span style="padding: 0.375rem 0.75rem; background: rgba(16, 185, 129, 0.1); color: var(--success); border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                        <i class="fas fa-check-circle" style="margin-right: 0.25rem;"></i>
                                        Actif
                                    </span>
                                    @break
                                @case('pending')
                                    <span style="padding: 0.375rem 0.75rem; background: rgba(245, 158, 11, 0.1); color: var(--warning); border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                        <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>
                                        En attente
                                    </span>
                                    @break
                                @case('inactive')
                                    <span style="padding: 0.375rem 0.75rem; background: rgba(239, 68, 68, 0.1); color: var(--danger); border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                        <i class="fas fa-times-circle" style="margin-right: 0.25rem;"></i>
                                        Inactif
                                    </span>
                                    @break
                                @default
                                    <span style="padding: 0.375rem 0.75rem; background: rgba(107, 114, 128, 0.1); color: var(--secondary); border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                        Inconnu
                                    </span>
                            @endswitch
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                @php
                                    $rating = $service->average_rating ?? 0;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                @endphp
                                
                                <div style="display: flex; gap: 0.1rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $fullStars)
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 0.875rem;"></i>
                                        @elseif($i == $fullStars + 1 && $hasHalfStar)
                                            <i class="fas fa-star-half-alt" style="color: #fbbf24; font-size: 0.875rem;"></i>
                                        @else
                                            <i class="far fa-star" style="color: #d1d5db; font-size: 0.875rem;"></i>
                                        @endif
                                    @endfor
                                </div>
                                
                                <span style="font-weight: 500; color: var(--dark); font-size: 0.875rem;">{{ number_format($rating, 1) }}</span>
                                <span style="color: var(--secondary); font-size: 0.8rem;">({{ $service->reviews_count ?? 0 }})</span>
                            </div>
                        </td>
                        <td style="color: var(--secondary); font-size: 0.875rem;">
                            {{ $service->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('administrateur.services.show', $service->id) }}" class="btn btn-outline" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('administrateur.services.edit', $service->id) }}" class="btn btn-info" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if($service->status === 'pending')
                                    <button onclick="approveService({{ $service->id }})" class="btn btn-success" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                
                                @if($service->status === 'active')
                                    <button onclick="deactivateService({{ $service->id }})" class="btn btn-warning" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Désactiver">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                @else
                                    <button onclick="activateService({{ $service->id }})" class="btn btn-success" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Activer">
                                        <i class="fas fa-play"></i>
                                    </button>
                                @endif
                                
                                <button onclick="deleteService({{ $service->id }})" class="btn btn-danger" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem; color: var(--secondary);">
                            <i class="fas fa-briefcase" style="font-size: 3rem; margin-bottom: 1rem; color: #e2e8f0;"></i>
                            <div style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">Aucun service trouvé</div>
                            <div>Essayez de modifier vos critères de recherche</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($services && $services->hasPages())
        <div style="padding: 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: between; align-items: center;">
            <div style="color: var(--secondary); font-size: 0.875rem;">
                Affichage de {{ $services->firstItem() }} à {{ $services->lastItem() }} sur {{ $services->total() }} résultats
            </div>
            <div>
                {{ $services->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Bulk Actions -->
<div id="bulkActions" style="position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: white; padding: 1rem 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); display: none; z-index: 1000;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="font-weight: 500;">Actions groupées :</span>
        <button class="btn btn-success" onclick="bulkApprove()">
            <i class="fas fa-check"></i>
            Approuver
        </button>
        <button class="btn btn-warning" onclick="bulkDeactivate()">
            <i class="fas fa-pause"></i>
            Désactiver
        </button>
        <button class="btn btn-danger" onclick="bulkDelete()">
            <i class="fas fa-trash"></i>
            Supprimer
        </button>
        <button class="btn btn-outline" onclick="clearSelection()">
            <i class="fas fa-times"></i>
            Annuler
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle filters panel
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

// Clear filters
function clearFilters() {
    window.location.href = '{{ route("administrateur.services.index") }}';
}

// Change items per page
function changePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    window.location.href = url.toString();
}

// Toggle all checkboxes
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.service-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
    updateBulkActions();
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.service-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
}

// Add event listeners to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.service-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Clear selection
function clearSelection() {
    const checkboxes = document.querySelectorAll('.service-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
}

// Service actions
function approveService(serviceId) {
    if (confirm('Êtes-vous sûr de vouloir approuver ce service ?')) {
        // Implement approve service logic
        console.log('Approving service:', serviceId);
    }
}

function activateService(serviceId) {
    if (confirm('Êtes-vous sûr de vouloir activer ce service ?')) {
        // Implement activate service logic
        console.log('Activating service:', serviceId);
    }
}

function deactivateService(serviceId) {
    if (confirm('Êtes-vous sûr de vouloir désactiver ce service ?')) {
        // Implement deactivate service logic
        console.log('Deactivating service:', serviceId);
    }
}

function deleteService(serviceId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce service ? Cette action est irréversible.')) {
        // Implement delete service logic
        console.log('Deleting service:', serviceId);
    }
}

// Bulk actions
function bulkApprove() {
    const checkedBoxes = document.querySelectorAll('.service-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir approuver ${checkedBoxes.length} service(s) ?`)) {
        // Implement bulk approve logic
        console.log('Bulk approving services');
    }
}

function bulkDeactivate() {
    const checkedBoxes = document.querySelectorAll('.service-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir désactiver ${checkedBoxes.length} service(s) ?`)) {
        // Implement bulk deactivate logic
        console.log('Bulk deactivating services');
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.service-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${checkedBoxes.length} service(s) ? Cette action est irréversible.`)) {
        // Implement bulk delete logic
        console.log('Bulk deleting services');
    }
}

// Export services
function exportServices() {
    // Implement export logic
    console.log('Exporting services');
}
</script>
@endpush
@extends('layouts.admin-modern')

@section('page-title', 'Gestion des Catégories')

@section('content')
<!-- Header Actions -->
<div style="display: flex; justify-content: between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--dark); margin: 0;">Catégories</h2>
        <p style="color: var(--secondary); margin: 0.5rem 0 0 0;">Organisez les services par catégories</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('administrateur.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nouvelle Catégorie
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
                <div class="stat-title">Total Catégories</div>
                <div class="stat-value">{{ $categories->total() ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+3% ce mois</span>
                </div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-tags"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-title">Catégories Actives</div>
                <div class="stat-value">{{ $categories->where('is_active', true)->count() ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+5% ce mois</span>
                </div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-header">
            <div>
                <div class="stat-title">Services Associés</div>
                <div class="stat-value">{{ $totalServices ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+15% ce mois</span>
                </div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-briefcase"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-title">Catégories Populaires</div>
                <div class="stat-value">{{ $popularCategories ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8% ce mois</span>
                </div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-fire"></i>
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
    <form action="{{ route('administrateur.categories.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; padding: 1rem 0;">
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Nom de la catégorie</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Rechercher par nom..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Statut</label>
            <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Tous</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Trier par</label>
            <select name="sort" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom</option>
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                <option value="services_count" {{ request('sort') == 'services_count' ? 'selected' : '' }}>Nombre de services</option>
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

<!-- Categories Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    @forelse($categories ?? [] as $category)
        <div class="chart-card" style="position: relative;">
            <div style="position: absolute; top: 1rem; right: 1rem; display: flex; gap: 0.5rem;">
                <input type="checkbox" name="selected_categories[]" value="{{ $category->id }}" class="category-checkbox" style="margin-right: 0.5rem;">
                
                @if($category->is_active)
                    <span style="padding: 0.25rem 0.5rem; background: rgba(16, 185, 129, 0.1); color: var(--success); border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                        <i class="fas fa-check-circle" style="margin-right: 0.25rem;"></i>
                        Actif
                    </span>
                @else
                    <span style="padding: 0.25rem 0.5rem; background: rgba(239, 68, 68, 0.1); color: var(--danger); border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                        <i class="fas fa-times-circle" style="margin-right: 0.25rem;"></i>
                        Inactif
                    </span>
                @endif
            </div>
            
            <div style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    @if($category->icon)
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                            <i class="{{ $category->icon }}"></i>
                        </div>
                    @else
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.2rem;">
                            {{ substr($category->name, 0, 1) }}
                        </div>
                    @endif
                    
                    <div style="flex: 1;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--dark); margin: 0 0 0.25rem 0;">{{ $category->name }}</h3>
                        <p style="color: var(--secondary); font-size: 0.875rem; margin: 0;">{{ Str::limit($category->description, 60) }}</p>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">{{ $category->services_count ?? 0 }}</div>
                        <div style="font-size: 0.8rem; color: var(--secondary);">Services</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--success);">{{ $category->active_services_count ?? 0 }}</div>
                        <div style="font-size: 0.8rem; color: var(--secondary);">Actifs</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--info);">{{ $category->prestataires_count ?? 0 }}</div>
                        <div style="font-size: 0.8rem; color: var(--secondary);">Prestataires</div>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: between; align-items: center; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                    <div style="font-size: 0.8rem; color: var(--secondary);">
                        Créé le {{ $category->created_at->format('d/m/Y') }}
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('administrateur.categories.show', $category->id) }}" class="btn btn-outline" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        <a href="{{ route('administrateur.categories.edit', $category->id) }}" class="btn btn-info" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        @if($category->is_active)
                            <button onclick="deactivateCategory({{ $category->id }})" class="btn btn-warning" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Désactiver">
                                <i class="fas fa-pause"></i>
                            </button>
                        @else
                            <button onclick="activateCategory({{ $category->id }})" class="btn btn-success" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Activer">
                                <i class="fas fa-play"></i>
                            </button>
                        @endif
                        
                        <button onclick="deleteCategory({{ $category->id }})" class="btn btn-danger" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--secondary);">
            <i class="fas fa-tags" style="font-size: 3rem; margin-bottom: 1rem; color: #e2e8f0;"></i>
            <div style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">Aucune catégorie trouvée</div>
            <div>Commencez par créer votre première catégorie</div>
            <a href="{{ route('administrateur.categories.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-plus"></i>
                Créer une catégorie
            </a>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($categories && $categories->hasPages())
    <div class="chart-card" style="padding: 1.5rem; display: flex; justify-content: between; align-items: center;">
        <div style="color: var(--secondary); font-size: 0.875rem;">
            Affichage de {{ $categories->firstItem() }} à {{ $categories->lastItem() }} sur {{ $categories->total() }} résultats
        </div>
        <div>
            {{ $categories->links() }}
        </div>
    </div>
@endif

<!-- Bulk Actions -->
<div id="bulkActions" style="position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: white; padding: 1rem 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); display: none; z-index: 1000;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="font-weight: 500;">Actions groupées :</span>
        <button class="btn btn-success" onclick="bulkActivate()">
            <i class="fas fa-play"></i>
            Activer
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
    window.location.href = '{{ route("administrateur.categories.index") }}';
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
}

// Add event listeners to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.category-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Clear selection
function clearSelection() {
    const checkboxes = document.querySelectorAll('.category-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
}

// Category actions
function activateCategory(categoryId) {
    if (confirm('Êtes-vous sûr de vouloir activer cette catégorie ?')) {
        // Implement activate category logic
        console.log('Activating category:', categoryId);
    }
}

function deactivateCategory(categoryId) {
    if (confirm('Êtes-vous sûr de vouloir désactiver cette catégorie ?')) {
        // Implement deactivate category logic
        console.log('Deactivating category:', categoryId);
    }
}

function deleteCategory(categoryId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible.')) {
        // Implement delete category logic
        console.log('Deleting category:', categoryId);
    }
}

// Bulk actions
function bulkActivate() {
    const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir activer ${checkedBoxes.length} catégorie(s) ?`)) {
        // Implement bulk activate logic
        console.log('Bulk activating categories');
    }
}

function bulkDeactivate() {
    const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir désactiver ${checkedBoxes.length} catégorie(s) ?`)) {
        // Implement bulk deactivate logic
        console.log('Bulk deactivating categories');
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${checkedBoxes.length} catégorie(s) ? Cette action est irréversible.`)) {
        // Implement bulk delete logic
        console.log('Bulk deleting categories');
    }
}
</script>
@endpush
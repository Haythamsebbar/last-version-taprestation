@extends('layouts.admin-modern')

@section('page-title', 'Gestion des Compétences')

@section('content')
<!-- Header Actions -->
<div style="display: flex; justify-content: between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--dark); margin: 0;">Compétences</h2>
        <p style="color: var(--secondary); margin: 0.5rem 0 0 0;">Gérez les compétences et expertises des prestataires</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('administrateur.skills.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nouvelle Compétence
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
                <div class="stat-title">Total Compétences</div>
                <div class="stat-value">{{ $skills->total() ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+6% ce mois</span>
                </div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-cogs"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-title">Compétences Actives</div>
                <div class="stat-value">{{ $skills->where('is_active', true)->count() ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+4% ce mois</span>
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
                <div class="stat-title">Prestataires Associés</div>
                <div class="stat-value">{{ $totalPrestataires ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12% ce mois</span>
                </div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-title">Compétences Populaires</div>
                <div class="stat-value">{{ $popularSkills ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+18% ce mois</span>
                </div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-fire"></i>
            </div>
        </div>
    </div>
</div>

<!-- Skills Categories Chart -->
<div class="chart-card" style="margin-bottom: 2rem;">
    <div class="chart-header">
        <div class="chart-title">Répartition par Catégories</div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <select style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem;">
                <option>Toutes les catégories</option>
                <option>Technique</option>
                <option>Design</option>
                <option>Marketing</option>
                <option>Business</option>
            </select>
        </div>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            @php
                $categories = [
                    ['name' => 'Développement', 'count' => 45, 'color' => 'var(--primary)', 'icon' => 'fas fa-code'],
                    ['name' => 'Design', 'count' => 32, 'color' => 'var(--success)', 'icon' => 'fas fa-paint-brush'],
                    ['name' => 'Marketing', 'count' => 28, 'color' => 'var(--warning)', 'icon' => 'fas fa-bullhorn'],
                    ['name' => 'Business', 'count' => 21, 'color' => 'var(--info)', 'icon' => 'fas fa-briefcase'],
                    ['name' => 'Autres', 'count' => 15, 'color' => 'var(--secondary)', 'icon' => 'fas fa-ellipsis-h']
                ];
            @endphp
            
            @foreach($categories as $category)
                <div style="text-align: center; padding: 1rem; background: rgba(99, 102, 241, 0.02); border-radius: 12px; border: 1px solid rgba(99, 102, 241, 0.1);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: {{ $category['color'] }}; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; margin: 0 auto 1rem;">
                        <i class="{{ $category['icon'] }}"></i>
                    </div>
                    <div style="font-weight: 600; color: var(--dark); margin-bottom: 0.5rem;">{{ $category['name'] }}</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: {{ $category['color'] }}; margin-bottom: 0.25rem;">{{ $category['count'] }}</div>
                    <div style="font-size: 0.8rem; color: var(--secondary);">compétences</div>
                </div>
            @endforeach
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
    <form action="{{ route('administrateur.skills.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; padding: 1rem 0;">
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Nom de la compétence</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Rechercher par nom..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Catégorie</label>
            <select name="category" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Toutes les catégories</option>
                <option value="development" {{ request('category') == 'development' ? 'selected' : '' }}>Développement</option>
                <option value="design" {{ request('category') == 'design' ? 'selected' : '' }}>Design</option>
                <option value="marketing" {{ request('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                <option value="business" {{ request('category') == 'business' ? 'selected' : '' }}>Business</option>
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Statut</label>
            <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Tous</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
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

<!-- Skills Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    @forelse($skills ?? [] as $skill)
        <div class="chart-card" style="position: relative; transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="position: absolute; top: 1rem; right: 1rem; display: flex; gap: 0.5rem; align-items: center;">
                <input type="checkbox" name="selected_skills[]" value="{{ $skill->id }}" class="skill-checkbox" style="margin-right: 0.5rem;">
                
                @if($skill->is_active)
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
                    @php
                        $categoryColors = [
                            'development' => 'var(--primary)',
                            'design' => 'var(--success)',
                            'marketing' => 'var(--warning)',
                            'business' => 'var(--info)',
                            'default' => 'var(--secondary)'
                        ];
                        $categoryIcons = [
                            'development' => 'fas fa-code',
                            'design' => 'fas fa-paint-brush',
                            'marketing' => 'fas fa-bullhorn',
                            'business' => 'fas fa-briefcase',
                            'default' => 'fas fa-cog'
                        ];
                        $color = $categoryColors[$skill->category ?? 'default'] ?? $categoryColors['default'];
                        $icon = $categoryIcons[$skill->category ?? 'default'] ?? $categoryIcons['default'];
                    @endphp
                    
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: {{ $color }}; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem;">
                        <i class="{{ $icon }}"></i>
                    </div>
                    
                    <div style="flex: 1;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--dark); margin: 0 0 0.25rem 0;">{{ $skill->name }}</h3>
                        <p style="color: var(--secondary); font-size: 0.875rem; margin: 0;">{{ Str::limit($skill->description, 50) }}</p>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="text-align: center; padding: 0.75rem; background: rgba(99, 102, 241, 0.05); border-radius: 8px;">
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary);">{{ $skill->prestataires_count ?? 0 }}</div>
                        <div style="font-size: 0.8rem; color: var(--secondary);">Prestataires</div>
                    </div>
                    <div style="text-align: center; padding: 0.75rem; background: rgba(16, 185, 129, 0.05); border-radius: 8px;">
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--success);">{{ $skill->services_count ?? 0 }}</div>
                        <div style="font-size: 0.8rem; color: var(--secondary);">Services</div>
                    </div>
                </div>
                
                @if($skill->category)
                    <div style="margin-bottom: 1rem;">
                        <span style="padding: 0.375rem 0.75rem; background: rgba(99, 102, 241, 0.1); color: var(--primary); border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                            {{ ucfirst($skill->category) }}
                        </span>
                    </div>
                @endif
                
                <div style="display: flex; justify-content: between; align-items: center; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                    <div style="font-size: 0.8rem; color: var(--secondary);">
                        Créé le {{ $skill->created_at->format('d/m/Y') }}
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('administrateur.skills.show', $skill->id) }}" class="btn btn-outline" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        <a href="{{ route('administrateur.skills.edit', $skill->id) }}" class="btn btn-info" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        @if($skill->is_active)
                            <button onclick="deactivateSkill({{ $skill->id }})" class="btn btn-warning" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Désactiver">
                                <i class="fas fa-pause"></i>
                            </button>
                        @else
                            <button onclick="activateSkill({{ $skill->id }})" class="btn btn-success" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Activer">
                                <i class="fas fa-play"></i>
                            </button>
                        @endif
                        
                        <button onclick="deleteSkill({{ $skill->id }})" class="btn btn-danger" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--secondary);">
            <i class="fas fa-cogs" style="font-size: 3rem; margin-bottom: 1rem; color: #e2e8f0;"></i>
            <div style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">Aucune compétence trouvée</div>
            <div>Commencez par créer votre première compétence</div>
            <a href="{{ route('administrateur.skills.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-plus"></i>
                Créer une compétence
            </a>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($skills && $skills->hasPages())
    <div class="chart-card" style="padding: 1.5rem; display: flex; justify-content: between; align-items: center;">
        <div style="color: var(--secondary); font-size: 0.875rem;">
            Affichage de {{ $skills->firstItem() }} à {{ $skills->lastItem() }} sur {{ $skills->total() }} résultats
        </div>
        <div>
            {{ $skills->links() }}
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
    window.location.href = '{{ route("administrateur.skills.index") }}';
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.skill-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
}

// Add event listeners to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.skill-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Clear selection
function clearSelection() {
    const checkboxes = document.querySelectorAll('.skill-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
}

// Skill actions
function activateSkill(skillId) {
    if (confirm('Êtes-vous sûr de vouloir activer cette compétence ?')) {
        // Implement activate skill logic
        console.log('Activating skill:', skillId);
    }
}

function deactivateSkill(skillId) {
    if (confirm('Êtes-vous sûr de vouloir désactiver cette compétence ?')) {
        // Implement deactivate skill logic
        console.log('Deactivating skill:', skillId);
    }
}

function deleteSkill(skillId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette compétence ? Cette action est irréversible.')) {
        // Implement delete skill logic
        console.log('Deleting skill:', skillId);
    }
}

// Bulk actions
function bulkActivate() {
    const checkedBoxes = document.querySelectorAll('.skill-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir activer ${checkedBoxes.length} compétence(s) ?`)) {
        // Implement bulk activate logic
        console.log('Bulk activating skills');
    }
}

function bulkDeactivate() {
    const checkedBoxes = document.querySelectorAll('.skill-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir désactiver ${checkedBoxes.length} compétence(s) ?`)) {
        // Implement bulk deactivate logic
        console.log('Bulk deactivating skills');
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.skill-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${checkedBoxes.length} compétence(s) ? Cette action est irréversible.`)) {
        // Implement bulk delete logic
        console.log('Bulk deleting skills');
    }
}
</script>
@endpush
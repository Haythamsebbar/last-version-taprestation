@extends('layouts.admin-modern')

@section('title', 'Gestion des Prestataires')
@section('page-title', 'Gestion des Prestataires')

@section('content')
<div class="container-fluid">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestion des Prestataires</h1>
            <p class="text-muted mb-0">Gérez et supervisez tous les prestataires de la plateforme</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('administrateur.prestataires.pending') }}" class="btn btn-warning d-flex align-items-center">
                <i class="fas fa-clock me-2"></i>En attente ({{ $stats['pending'] }})
            </a>
            <button type="button" class="btn btn-outline-secondary d-flex align-items-center" onclick="toggleFilters()">
                <i class="fas fa-filter me-2"></i>Filtres
            </button>
            <button type="button" class="btn btn-primary d-flex align-items-center" onclick="exportPrestataires()">
                <i class="fas fa-download me-2"></i>Exporter
            </button>
        </div>
    </div>

<!-- Stats Cards -->
<div class="stats-grid" style="margin-bottom: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
    <div class="stat-card primary">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Prestataires</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stats-trend">
                    <i class="fas fa-chart-line"></i> Total inscrit
                </div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-title">Prestataires Approuvés</div>
                <div class="stat-value">{{ $stats['approved'] }}</div>
                <div class="stats-trend">
                    <i class="fas fa-thumbs-up"></i> {{ $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0 }}% du total
                </div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-title">Prestataires en Attente</div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stats-trend">
                    <i class="fas fa-hourglass-half"></i> {{ $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 1) : 0 }}% du total
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
                <div class="stat-title">Nouveaux ce mois</div>
                <div class="stat-value">{{ $stats['new_this_month'] }}</div>
                <div class="stats-trend">
                    <i class="fas fa-calendar-alt"></i> {{ now()->format('F Y') }}
                </div>
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
    <form action="{{ route('administrateur.prestataires.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; padding: 1rem 0;">
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Nom</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Rechercher par nom..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Email</label>
            <input type="email" name="email" value="{{ request('email') }}" placeholder="Rechercher par email..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Catégorie</label>
            <select name="category_id" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
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
                <option value="services_count" {{ request('sort') == 'services_count' ? 'selected' : '' }}>Nombre de services</option>
                <option value="orders_count" {{ request('sort') == 'orders_count' ? 'selected' : '' }}>Nombre de commandes</option>
                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Note moyenne</option>
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
            <a href="{{ route('administrateur.prestataires.index') }}" class="btn btn-outline">
                <i class="fas fa-redo"></i>
                Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- Items Per Page & Export -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
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
    
    <button class="btn btn-outline" onclick="exportPrestataires()">
        <i class="fas fa-download"></i>
        <span class="d-none d-sm-inline">Exporter</span>
    </button>
</div>

<!-- Main Content -->
<div class="content-card">
    <!-- Cards Layout -->
    <div class="prestataires-grid">
        <!-- Select All Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; padding: 1rem; background: var(--light); border-radius: 8px;">
            <div style="display: flex; align-items: center;">
                <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()" style="margin-right: 0.5rem;">
                <label for="selectAll" style="font-weight: 600; margin: 0;">Sélectionner tout</label>
            </div>
            <div style="color: var(--secondary);">
                {{ $prestataires->count() }} prestataire(s) affiché(s)
            </div>
        </div>

        @forelse($prestataires as $prestataire)
            <div class="prestataire-card {{ $prestataire->user->created_at->isCurrentMonth() ? 'new' : '' }}">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <!-- Checkbox -->
                    <div>
                        <input type="checkbox" value="{{ $prestataire->id }}" class="prestataire-checkbox" onchange="updateBulkActionsVisibility()">
                    </div>
                    
                    <!-- Avatar & Basic Info -->
                    <div>
                        <div class="avatar" style="position: relative;">
                            @if($prestataire->user->profile_photo_path)
                                <img src="{{ asset('storage/' . $prestataire->user->profile_photo_path) }}" alt="{{ $prestataire->user->name }}" class="prestataire-avatar">
                            @elseif($prestataire->photo)
                                <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="{{ $prestataire->user->name }}" class="prestataire-avatar">
                            @else
                                <div class="avatar-initials" style="width: 64px; height: 64px; border-radius: 16px; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 600;">
                                    {{ substr($prestataire->user->name, 0, 1) }}
                                </div>
                            @endif
                            @if($prestataire->isVerified())
                                <div style="position: absolute; top: -4px; right: -4px; width: 20px; height: 20px; background: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white;">
                                    <i class="fas fa-check" style="font-size: 10px; color: white;"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Main Info -->
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <h5 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--dark);">{{ $prestataire->user->name }}</h5>
                            @if($prestataire->isVerified())
                                <span class="badge success" style="font-size: 10px; padding: 4px 8px;">
                                    <i class="fas fa-check"></i> Vérifié
                                </span>
                            @endif
                        </div>
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">{{ $prestataire->user->email }}</div>
                        
                        <!-- Badges -->
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">

                            
                            @if($prestataire->user->created_at->isCurrentMonth())
                                <span class="badge info">
                                    <i class="fas fa-star"></i> Nouveau ce mois
                                </span>
                            @endif
                            
                            @if($prestataire->category)
                                <span class="badge primary">{{ $prestataire->category->name }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div style="display: none;" class="d-md-block">
                        <div class="prestataire-stats" style="display: flex; gap: 1.5rem;">
                            <div style="text-align: center;">
                                <div style="font-size: 1.25rem; font-weight: 600; color: var(--dark);">{{ $prestataire->services_count ?? $prestataire->services->count() }}</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">Services</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 1.25rem; font-weight: 600; color: var(--dark);">{{ $prestataire->orders_count ?? 0 }}</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">Commandes</div>
                            </div>
                            <div style="text-align: center;">
                                <div class="rating-display" style="display: flex; align-items: center; gap: 0.25rem; justify-content: center;">
                                    <div class="rating-stars" style="color: #fbbf24;">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= ($prestataire->rating ?? 0))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span style="font-weight: 600; color: var(--dark);">{{ number_format($prestataire->rating ?? 0, 1) }}</span>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">Note</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status & Actions -->
                    <div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <!-- Status -->
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                @if($prestataire->user->blocked_at)
                                    <span style="width: 8px; height: 8px; background: var(--danger); border-radius: 50%; display: inline-block;"></span>
                                    <span style="color: var(--danger); font-weight: 600; font-size: 0.875rem;">Bloqué</span>
                                @else
                                    <span style="width: 8px; height: 8px; background: var(--success); border-radius: 50%; display: inline-block;"></span>
                                    <span style="color: var(--success); font-weight: 600; font-size: 0.875rem;">Actif</span>
                                @endif
                            </div>
                            
                            <!-- Actions -->
                            <div class="actions-dropdown">
                                <button class="btn btn-outline" onclick="toggleDropdown('dropdown-{{ $prestataire->id }}')" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                    <i class="fas fa-cog"></i> Actions
                                </button>
                                <div class="dropdown-menu" id="dropdown-{{ $prestataire->id }}" style="display: none;">
                                    <a href="{{ route('administrateur.prestataires.show', $prestataire->id) }}" class="dropdown-item">
                                        <i class="fas fa-eye"></i> Voir profil
                                    </a>

                                    @if(auth()->id() != $prestataire->user_id)
                                        @if($prestataire->user->blocked_at)
                                            <button onclick="toggleBlockPrestataire('{{ $prestataire->id }}', 'unblock')" class="dropdown-item">
                                                <i class="fas fa-unlock"></i> Débloquer
                                            </button>
                                        @else
                                            <button onclick="toggleBlockPrestataire('{{ $prestataire->id }}', 'block')" class="dropdown-item">
                                                <i class="fas fa-lock"></i> Désactiver
                                            </button>
                                        @endif
                                        <button onclick="deletePrestataire('{{ $prestataire->id }}')" class="dropdown-item text-danger">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Stats -->
                <div style="display: block; margin-top: 1rem;" class="d-md-none">
                    <div style="display: flex; justify-content: space-around; text-align: center;">
                        <div>
                            <div style="font-size: 1.125rem; font-weight: 600; color: var(--dark);">{{ $prestataire->services_count ?? $prestataire->services->count() }}</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Services</div>
                        </div>
                        <div>
                            <div style="font-size: 1.125rem; font-weight: 600; color: var(--dark);">{{ $prestataire->orders_count ?? 0 }}</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Commandes</div>
                        </div>
                        <div>
                            <div class="rating-display" style="display: flex; align-items: center; gap: 0.25rem; justify-content: center;">
                                <div class="rating-stars" style="color: #fbbf24;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= ($prestataire->rating ?? 0))
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span style="font-weight: 600; color: var(--dark);">{{ number_format($prestataire->rating ?? 0, 1) }}</span>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Note</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 3rem 0;">
                <div style="color: var(--secondary);">
                    <i class="fas fa-users" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <h4>Aucun prestataire trouvé</h4>
                    <p>Aucun prestataire ne correspond aux critères de recherche.</p>
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div style="padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 0.875rem; color: var(--secondary);">
            Affichage de {{ $prestataires->firstItem() ?? 0 }} à {{ $prestataires->lastItem() ?? 0 }} sur {{ $prestataires->total() }} entrées
        </div>
        {{ $prestataires->appends(request()->query())->links() }}
    </div>
</div>

<!-- Bulk Actions -->
<div id="bulkActions" style="position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: white; padding: 1rem 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); display: none; z-index: 1000; max-width: 90vw;">
    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; justify-content: center;">
        <span id="selectedCount" style="font-weight: 500; white-space: nowrap;">0 sélectionné(s)</span>
        <button class="btn btn-outline" onclick="clearSelection()">
            <i class="fas fa-times"></i>
            <span class="d-none d-sm-inline">Annuler</span>
        </button>
        <button class="btn btn-success" onclick="bulkUnblock()">
            <i class="fas fa-unlock"></i>
            <span class="d-none d-sm-inline">Débloquer</span>
        </button>
        <button class="btn btn-warning" onclick="bulkBlock()">
            <i class="fas fa-lock"></i>
            <span class="d-none d-sm-inline">Bloquer</span>
        </button>
        <button class="btn btn-danger" onclick="bulkDelete()">
            <i class="fas fa-trash"></i>
            <span class="d-none d-sm-inline">Supprimer</span>
        </button>
    </div>
</div>

<style>
    :root {
        --primary: #3b82f6;
        --primary-dark: #2563eb;
        --secondary: #6b7280;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #06b6d4;
        --light: #f8fafc;
        --dark: #1f2937;
        --border: #e5e7eb;
        --text-muted: #6b7280;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .stat-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border: none;
        border-radius: 16px;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        padding: 1.5rem;
    }
    
    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .stat-title {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stats-trend {
        font-size: 0.75rem;
        opacity: 0.8;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow-hover);
    }
    
    .stat-card:hover::before {
        opacity: 1;
    }
    
    .stat-card.success {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
    }
    
    .stat-card.info {
        background: linear-gradient(135deg, var(--info) 0%, #0891b2 100%);
    }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        backdrop-filter: blur(10px);
    }
    
    .prestataire-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }
    
    .prestataire-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--primary);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .prestataire-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--card-shadow-hover);
    }
    
    .prestataire-card:hover::before {
        opacity: 1;
    }
    
    .prestataire-avatar {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        object-fit: cover;
        border: 3px solid var(--border);
        transition: all 0.3s ease;
    }
    
    .prestataire-card:hover .prestataire-avatar {
        border-color: var(--primary);
        transform: scale(1.05);
    }
    
    .rating-display {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
    }
    
    .rating-stars {
        color: #fbbf24;
        font-size: 16px;
    }
    
    .rating-value {
        font-weight: 600;
        color: var(--dark);
    }
    
    /* Styles responsifs supplémentaires */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr !important;
        }
        
        .table th, .table td {
            padding: 0.5rem !important;
            font-size: 0.875rem;
        }
        
        .avatar {
            width: 32px !important;
            height: 32px !important;
        }
        
        .avatar-initials {
            font-size: 0.75rem !important;
        }
        
        .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .dropdown-menu {
            position: absolute;
            right: 0;
            left: auto;
            min-width: 150px;
        }
        
        .prestataire-avatar {
            width: 56px;
            height: 56px;
        }
        
        .stats-icon {
            width: 48px;
            height: 48px;
            font-size: 24px;
        }
    }
    
    @media (max-width: 576px) {
        .content {
            padding: 1rem !important;
        }
        
        .chart-card, .content-card {
            margin: 0 -0.5rem;
            border-radius: 0;
        }
        
        #bulkActions {
            bottom: 1rem !important;
            left: 1rem !important;
            right: 1rem !important;
            transform: none !important;
            max-width: none !important;
            width: auto !important;
        }
        
        .prestataire-card {
            padding: 16px;
        }
    }
    
    /* Amélioration des badges */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: 1px solid transparent;
    }
    
    .badge.success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
        border-color: rgba(16, 185, 129, 0.2);
    }
    
    .badge.warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
        border-color: rgba(245, 158, 11, 0.2);
    }
    
    .badge.info {
        background: rgba(6, 182, 212, 0.1);
        color: var(--info);
        border-color: rgba(6, 182, 212, 0.2);
    }
    
    .badge.primary {
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary);
        border-color: rgba(59, 130, 246, 0.2);
    }
    
    .badge.secondary {
        background: rgba(107, 114, 128, 0.1);
        color: var(--secondary);
        border-color: rgba(107, 114, 128, 0.2);
    }
    
    .badge.danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border-color: rgba(239, 68, 68, 0.2);
    }
    
    /* Amélioration des boutons d'action */
    .actions-dropdown {
        position: relative;
    }
    
    .dropdown-menu {
        background: white;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        padding: 0.5rem 0;
        min-width: 160px;
        z-index: 1000;
    }
    
    .dropdown-item {
        display: block;
        width: 100%;
        padding: 0.5rem 1rem;
        color: var(--dark);
        text-decoration: none;
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: var(--light);
    }
    
    .dropdown-item.text-danger {
        color: var(--danger);
    }
    
    .dropdown-item.text-danger:hover {
        background-color: rgba(239, 68, 68, 0.1);
    }
</style>

<script>
    // Toggle filters panel
    function toggleFilters() {
        const filtersPanel = document.getElementById('filtersPanel');
        filtersPanel.style.display = filtersPanel.style.display === 'none' ? 'block' : 'none';
    }
    
    // Clear filters
    function clearFilters() {
        window.location.href = '{{ route("administrateur.prestataires.index") }}';
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
        const checkboxes = document.querySelectorAll('.prestataire-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        
        updateBulkActionsVisibility();
    }
    
    // Update bulk actions visibility
    function updateBulkActionsVisibility() {
        const checkboxes = document.querySelectorAll('.prestataire-checkbox:checked');
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
        const checkboxes = document.querySelectorAll('.prestataire-checkbox');
        
        selectAll.checked = false;
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        updateBulkActionsVisibility();
    }
    
    // Toggle block prestataire
    function toggleBlockPrestataire(prestatairesId, action) {
        const message = action === 'block' ? 'Êtes-vous sûr de vouloir bloquer ce prestataire ?' : 'Êtes-vous sûr de vouloir débloquer ce prestataire ?';
        
        if (confirm(message)) {
            fetch(`{{ url('/administrateur/prestataires') }}/${prestatairesId}/toggle-block`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ action: action })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Erreur lors de l\'opération: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'opération');
            });
        }
    }
    
    // Delete prestataire
    function deletePrestataire(prestatairesId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce prestataire ? Cette action est irréversible.')) {
            fetch(`{{ url('/administrateur/prestataires') }}/${prestatairesId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Erreur lors de la suppression: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression');
            });
        }
    }
    
    // Bulk unblock
    function bulkUnblock() {
        const selectedIds = getSelectedIds();
        
        if (selectedIds.length === 0) {
            alert('Veuillez sélectionner au moins un prestataire.');
            return;
        }
        
        if (confirm(`Êtes-vous sûr de vouloir débloquer ${selectedIds.length} prestataire(s) ?`)) {
            fetch(`{{ url('/administrateur/prestataires/bulk-unblock') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    alert('Déblocage réussi!');
                    window.location.reload();
                } else {
                    alert('Erreur lors du déblocage: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur complète:', error);
                alert('Erreur lors du déblocage: ' + error.message);
            });
        }
    }
    
    // Bulk block
    function bulkBlock() {
        const selectedIds = getSelectedIds();
        
        if (selectedIds.length === 0) {
            alert('Veuillez sélectionner au moins un prestataire.');
            return;
        }
        
        if (confirm(`Êtes-vous sûr de vouloir bloquer ${selectedIds.length} prestataire(s) ?`)) {
            fetch(`{{ url('/administrateur/prestataires/bulk-block') }}`, {
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
                    window.location.reload();
                } else {
                    alert('Erreur lors du blocage: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors du blocage');
            });
        }
    }
    
    // Bulk delete
    function bulkDelete() {
        const selectedIds = getSelectedIds();
        
        if (selectedIds.length === 0) {
            alert('Veuillez sélectionner au moins un prestataire.');
            return;
        }
        
        if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedIds.length} prestataire(s) ? Cette action est irréversible.`)) {
            fetch(`{{ url('/administrateur/prestataires/bulk-delete') }}`, {
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
                    window.location.reload();
                } else {
                    alert('Erreur lors de la suppression: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression');
            });
        }
    }
    
    // Get selected IDs
    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.prestataire-checkbox:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.value);
    }
    
    // Export prestataires
function exportPrestataires() {
    window.location.href = '{{ route("administrateur.prestataires.export") }}' + window.location.search;
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
@endsection
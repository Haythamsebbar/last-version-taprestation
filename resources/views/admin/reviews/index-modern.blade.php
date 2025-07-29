@extends('layouts.admin-modern')

@section('page-title', 'Gestion des Avis')

@section('content')
<!-- Header Actions -->
<div style="display: flex; justify-content: between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--dark); margin: 0;">Avis et Évaluations</h2>
        <p style="color: var(--secondary); margin: 0.5rem 0 0 0;">Modérez les avis clients et gérez la qualité du service</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <button class="btn btn-outline" onclick="toggleFilters()">
            <i class="fas fa-filter"></i>
            Filtres
        </button>
        <button class="btn btn-primary" onclick="exportReviews()">
            <i class="fas fa-download"></i>
            Exporter
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card primary">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Avis</div>
                <div class="stat-value">{{ $reviews->total() ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+15% ce mois</span>
                </div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-title">Note Moyenne</div>
                <div class="stat-value">{{ number_format($averageRating ?? 4.2, 1) }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+0.2 ce mois</span>
                </div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-thumbs-up"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-title">En Attente de Modération</div>
                <div class="stat-value">{{ $reviews->where('status', 'pending')->count() ?? 0 }}</div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down"></i>
                    <span>-5% ce mois</span>
                </div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card danger">
        <div class="stat-header">
            <div>
                <div class="stat-title">Avis Signalés</div>
                <div class="stat-value">{{ $reviews->where('is_reported', true)->count() ?? 0 }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-down"></i>
                    <span>-8% ce mois</span>
                </div>
            </div>
            <div class="stat-icon danger">
                <i class="fas fa-flag"></i>
            </div>
        </div>
    </div>
</div>

<!-- Rating Distribution Chart -->
<div class="chart-card" style="margin-bottom: 2rem;">
    <div class="chart-header">
        <div class="chart-title">Distribution des Notes</div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <select style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem;">
                <option>Ce mois</option>
                <option>3 derniers mois</option>
                <option>Cette année</option>
            </select>
        </div>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem;">
            @for($i = 5; $i >= 1; $i--)
                @php
                    $count = $reviews->where('rating', $i)->count() ?? 0;
                    $percentage = $reviews->count() > 0 ? ($count / $reviews->count()) * 100 : 0;
                @endphp
                <div style="text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.25rem; margin-bottom: 0.5rem;">
                        <span style="font-weight: 600;">{{ $i }}</span>
                        <i class="fas fa-star" style="color: #fbbf24; font-size: 0.875rem;"></i>
                    </div>
                    <div style="height: 100px; background: #f1f5f9; border-radius: 4px; position: relative; margin-bottom: 0.5rem;">
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: {{ $percentage }}%; background: linear-gradient(180deg, 
                            @if($i >= 4) var(--success) @elseif($i >= 3) var(--warning) @else var(--danger) @endif 0%, 
                            @if($i >= 4) #059669 @elseif($i >= 3) #d97706 @else #dc2626 @endif 100%); border-radius: 4px; transition: height 0.3s ease;"></div>
                    </div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">{{ $count }}</div>
                    <div style="font-size: 0.8rem; color: var(--secondary);">{{ number_format($percentage, 1) }}%</div>
                </div>
            @endfor
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
    <form action="{{ route('administrateur.reviews.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; padding: 1rem 0;">
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Client</label>
            <input type="text" name="client" value="{{ request('client') }}" placeholder="Nom du client..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Service</label>
            <input type="text" name="service" value="{{ request('service') }}" placeholder="Nom du service..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Note</label>
            <select name="rating" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Toutes les notes</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 étoiles</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 étoiles</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 étoiles</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 étoiles</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 étoile</option>
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Statut</label>
            <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                <option value="">Tous</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                <option value="reported" {{ request('status') == 'reported' ? 'selected' : '' }}>Signalé</option>
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

<!-- Reviews List -->
<div class="table-card">
    <div class="table-header">
        <div class="table-title">Liste des avis ({{ $reviews->total() ?? 0 }})</div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <select onchange="changePerPage(this.value)" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem;">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 par page</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 par page</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 par page</option>
            </select>
        </div>
    </div>
    
    <div style="padding: 1.5rem;">
        <div style="display: grid; gap: 1.5rem;">
            @forelse($reviews ?? [] as $review)
                <div style="background: #f8fafc; border-radius: 12px; padding: 1.5rem; border-left: 4px solid 
                    @if($review->rating >= 4) var(--success) @elseif($review->rating >= 3) var(--warning) @else var(--danger) @endif;">
                    <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <input type="checkbox" name="selected_reviews[]" value="{{ $review->id }}" class="review-checkbox">
                            
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, var(--info) 0%, #06b6d4 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.1rem;">
                                {{ $review->client ? substr($review->client->name, 0, 1) : 'C' }}
                            </div>
                            
                            <div>
                                <div style="font-weight: 600; color: var(--dark); margin-bottom: 0.25rem;">{{ $review->client_name }}</div>
                                <div style="font-size: 0.875rem; color: var(--secondary);">{{ $review->client_email ?? 'Email non disponible' }}</div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                                    <div style="display: flex; gap: 0.1rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star" style="color: #fbbf24; font-size: 0.875rem;"></i>
                                            @else
                                                <i class="far fa-star" style="color: #d1d5db; font-size: 0.875rem;"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span style="font-weight: 500; color: var(--dark); font-size: 0.875rem;">{{ $review->rating }}/5</span>
                                    <span style="color: var(--secondary); font-size: 0.8rem;">• {{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            @switch($review->status)
                                @case('approved')
                                    <span style="padding: 0.375rem 0.75rem; background: rgba(16, 185, 129, 0.1); color: var(--success); border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                        <i class="fas fa-check-circle" style="margin-right: 0.25rem;"></i>
                                        Approuvé
                                    </span>
                                    @break
                                @case('pending')
                                    <span style="padding: 0.375rem 0.75rem; background: rgba(245, 158, 11, 0.1); color: var(--warning); border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                        <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>
                                        En attente
                                    </span>
                                    @break
                                @case('rejected')
                                    <span style="padding: 0.375rem 0.75rem; background: rgba(239, 68, 68, 0.1); color: var(--danger); border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                        <i class="fas fa-times-circle" style="margin-right: 0.25rem;"></i>
                                        Rejeté
                                    </span>
                                    @break
                            @endswitch
                            
                            @if($review->is_reported)
                                <span style="padding: 0.375rem 0.75rem; background: rgba(239, 68, 68, 0.1); color: var(--danger); border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                    <i class="fas fa-flag" style="margin-right: 0.25rem;"></i>
                                    Signalé
                                </span>
                            @endif
                            
                            <div style="display: flex; gap: 0.5rem;">
                                @if($review->status === 'pending')
                                    <button onclick="approveReview({{ $review->id }})" class="btn btn-success" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectReview({{ $review->id }})" class="btn btn-danger" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Rejeter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                                
                                <button onclick="deleteReview({{ $review->id }})" class="btn btn-outline" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    @if($review->service)
                        <div style="margin-bottom: 1rem;">
                            <div style="font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Service évalué :</div>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                @if($review->service->image)
                                    <img src="{{ asset('storage/' . $review->service->image) }}" alt="{{ $review->service->title }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                @else
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem;">
                                        {{ substr($review->service->title, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight: 500; color: var(--dark); font-size: 0.875rem;">{{ $review->service->title }}</div>
                                    <div style="font-size: 0.8rem; color: var(--secondary);">par {{ $review->service->prestataire->user->name ?? 'Prestataire inconnu' }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="margin-bottom: 1rem;">
                            <div style="font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Service évalué :</div>
                            <div style="color: var(--secondary); font-style: italic;">Service non disponible</div>
                        </div>
                    @endif
                    
                    @if($review->comment)
                        <div style="background: white; padding: 1rem; border-radius: 8px; border-left: 3px solid var(--primary);">
                            <div style="font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Commentaire :</div>
                            <div style="color: var(--secondary); line-height: 1.6;">{{ $review->comment }}</div>
                        </div>
                    @endif
                    
                    @if($review->response)
                        <div style="background: rgba(16, 185, 129, 0.05); padding: 1rem; border-radius: 8px; border-left: 3px solid var(--success); margin-top: 1rem;">
                            <div style="font-weight: 500; color: var(--success); margin-bottom: 0.5rem;">
                                <i class="fas fa-reply" style="margin-right: 0.5rem;"></i>
                                Réponse du prestataire :
                            </div>
                            <div style="color: var(--dark); line-height: 1.6;">{{ $review->response }}</div>
                        </div>
                    @endif
                </div>
            @empty
                <div style="text-align: center; padding: 3rem; color: var(--secondary);">
                    <i class="fas fa-star" style="font-size: 3rem; margin-bottom: 1rem; color: #e2e8f0;"></i>
                    <div style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">Aucun avis trouvé</div>
                    <div>Essayez de modifier vos critères de recherche</div>
                </div>
            @endforelse
        </div>
    </div>
    
    @if($reviews && $reviews->hasPages())
        <div style="padding: 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: between; align-items: center;">
            <div style="color: var(--secondary); font-size: 0.875rem;">
                Affichage de {{ $reviews->firstItem() }} à {{ $reviews->lastItem() }} sur {{ $reviews->total() }} résultats
            </div>
            <div>
                {{ $reviews->links() }}
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
        <button class="btn btn-danger" onclick="bulkReject()">
            <i class="fas fa-times"></i>
            Rejeter
        </button>
        <button class="btn btn-outline" onclick="bulkDelete()">
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
    window.location.href = '{{ route("administrateur.reviews.index") }}';
}

// Change items per page
function changePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    window.location.href = url.toString();
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
}

// Add event listeners to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Clear selection
function clearSelection() {
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
}

// Review actions
function approveReview(reviewId) {
    if (confirm('Êtes-vous sûr de vouloir approuver cet avis ?')) {
        // Implement approve review logic
        console.log('Approving review:', reviewId);
    }
}

function rejectReview(reviewId) {
    if (confirm('Êtes-vous sûr de vouloir rejeter cet avis ?')) {
        // Implement reject review logic
        console.log('Rejecting review:', reviewId);
    }
}

function deleteReview(reviewId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet avis ? Cette action est irréversible.')) {
        // Implement delete review logic
        console.log('Deleting review:', reviewId);
    }
}

// Bulk actions
function bulkApprove() {
    const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir approuver ${checkedBoxes.length} avis ?`)) {
        // Implement bulk approve logic
        console.log('Bulk approving reviews');
    }
}

function bulkReject() {
    const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir rejeter ${checkedBoxes.length} avis ?`)) {
        // Implement bulk reject logic
        console.log('Bulk rejecting reviews');
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${checkedBoxes.length} avis ? Cette action est irréversible.`)) {
        // Implement bulk delete logic
        console.log('Bulk deleting reviews');
    }
}

// Export reviews
function exportReviews() {
    // Implement export logic
    console.log('Exporting reviews');
}
</script>
@endpush
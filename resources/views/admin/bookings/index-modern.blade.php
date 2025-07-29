@extends('layouts.admin-modern')

@section('page-title', 'Gestion des Réservations')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Header Actions -->
<div class="page-header">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--dark); margin: 0;">Réservations</h2>
        <p style="color: var(--secondary); margin: 0.5rem 0 0 0;">Gérez toutes les réservations de la plateforme</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('administrateur.bookings.export', request()->query()) }}" class="btn btn-outline">
            <i class="fas fa-download"></i>
            Exporter
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
                <div class="stat-title">Total</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-title">En attente</div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-title">Confirmées</div>
                <div class="stat-value">{{ $stats['confirmed'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-header">
            <div>
                <div class="stat-title">Terminées</div>
                <div class="stat-value">{{ $stats['completed'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-flag-checkered"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card danger">
        <div class="stat-header">
            <div>
                <div class="stat-title">Annulées</div>
                <div class="stat-value">{{ $stats['cancelled'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div id="filters" class="filters-section" style="display: none;">
    <form method="GET" action="{{ route('administrateur.bookings.index') }}">
        <div class="filters-grid">
            <div class="filter-group">
                <label for="status">Statut</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="date_from">Date de début</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            
            <div class="filter-group">
                <label for="date_to">Date de fin</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            
            <div class="filter-group">
                <label for="prestataire">Prestataire</label>
                <input type="text" name="prestataire" id="prestataire" class="form-control" placeholder="Nom du prestataire" value="{{ request('prestataire') }}">
            </div>
            
            <div class="filter-group">
                <label for="client">Client</label>
                <input type="text" name="client" id="client" class="form-control" placeholder="Nom du client" value="{{ request('client') }}">
            </div>
        </div>
        
        <div class="filters-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Filtrer
            </button>
            <a href="{{ route('administrateur.bookings.index') }}" class="btn btn-outline">
                <i class="fas fa-times"></i>
                Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="modern-card">
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Prestataire</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <span class="text-primary font-weight-bold">#{{ $booking->id }}</span>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $booking->client->user->name ?? 'N/A' }}</div>
                                    <div class="user-email">{{ $booking->client->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $booking->prestataire->user->name ?? 'N/A' }}</div>
                                    <div class="user-email">{{ $booking->prestataire->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="service-info">
                                <div class="service-title">{{ $booking->service->title ?? 'Service supprimé' }}</div>
                                @if($booking->service)
                                    <div class="service-category">{{ $booking->service->categories->first()->name ?? 'N/A' }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                <div class="booking-date">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : 'N/A' }}</div>
                                <div class="booking-time">{{ $booking->booking_time ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>
                            @switch($booking->status)
                                @case('pending')
                                    <span class="badge badge-warning">En attente</span>
                                    @break
                                @case('confirmed')
                                    <span class="badge badge-success">Confirmée</span>
                                    @break
                                @case('completed')
                                    <span class="badge badge-info">Terminée</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge badge-danger">Annulée</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ $booking->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if($booking->price)
                                <span class="price">{{ number_format($booking->price, 2) }} €</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('administrateur.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $booking->id }})" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times" style="font-size: 3rem; color: var(--secondary); margin-bottom: 1rem;"></i>
                                <h4>Aucune réservation trouvée</h4>
                                <p class="text-muted">Il n'y a aucune réservation correspondant à vos critères.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($bookings->hasPages())
        <div class="pagination-wrapper">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette réservation ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
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

@push('styles')
<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 0.875rem;
}

.user-details {
    flex: 1;
}

.user-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 0.875rem;
}

.user-email {
    color: var(--secondary);
    font-size: 0.75rem;
}

.service-info .service-title {
    font-weight: 600;
    color: var(--dark);
    font-size: 0.875rem;
}

.service-info .service-category {
    color: var(--secondary);
    font-size: 0.75rem;
}

.date-info .booking-date {
    font-weight: 600;
    color: var(--dark);
    font-size: 0.875rem;
}

.date-info .booking-time {
    color: var(--secondary);
    font-size: 0.75rem;
}

.price {
    font-weight: 600;
    color: var(--success);
    font-size: 0.875rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
}

.empty-state h4 {
    color: var(--dark);
    margin-bottom: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
function toggleFilters() {
    const filters = document.getElementById('filters');
    filters.style.display = filters.style.display === 'none' ? 'block' : 'none';
}

function confirmDelete(bookingId) {
    const form = document.getElementById('deleteForm');
    form.action = `/administrateur/bookings/${bookingId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
@extends('layouts.admin-modern')

@section('page-title', 'Tableau de bord')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Utilisateurs</div>
                <div class="stat-value">{{ $totalUsersCount ?? 0 }}</div>
                <div class="stat-change {{ $userChange >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas {{ $userChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    <span>{{ number_format($userChange, 1) }}% ce mois</span>
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
                <div class="stat-value">{{ $approvedPrestatairesCount ?? 0 }}</div>
                <div class="stat-change {{ $prestataireChange >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas {{ $prestataireChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    <span>{{ number_format($prestataireChange, 1) }}% ce mois</span>
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
                <div class="stat-title">En Attente</div>
                <div class="stat-value">{{ $pendingPrestatairesCount ?? 0 }}</div>
                <div class="stat-change {{ $pendingChange >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas {{ $pendingChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    <span>{{ number_format($pendingChange, 1) }}% ce mois</span>
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
                <div class="stat-title">Services Actifs</div>
                <div class="stat-value">{{ $activeServicesCount ?? 0 }}</div>
                <div class="stat-change {{ $serviceChange >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas {{ $serviceChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    <span>{{ number_format($serviceChange, 1) }}% ce mois</span>
                </div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-briefcase"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
    <!-- Main Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Évolution des Inscriptions</div>
            <div style="display: flex; gap: 1rem;" id="chart-period-buttons">
                <button class="btn btn-outline" data-period="7j">7j</button>
                <button class="btn btn-outline" data-period="30j">30j</button>
                <button class="btn btn-primary" data-period="1an">1an</button>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="registrationsChart"></canvas>
        </div>
    </div>
    
    <!-- Progress Stats -->
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Statistiques Détaillées</div>
        </div>
        <div style="padding: 1rem 0;">
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 500;">Demandes de services</span>
                    <span style="font-weight: 600; color: var(--primary);">{{ $totalClientRequests ?? 0 }}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar primary" style="width: 85%;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 500;">Services publiés</span>
                    <span style="font-weight: 600; color: var(--success);">{{ $totalServices ?? 0 }}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar success" style="width: 70%;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 500;">Messages échangés</span>
                    <span style="font-weight: 600; color: var(--info);">{{ $totalMessages ?? 0 }}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar info" style="width: 92%;"></div>
                </div>
            </div>
            
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 500;">Taux de satisfaction</span>
                    <span style="font-weight: 600; color: var(--warning);">{{ $satisfactionRate ?? 0 }}%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar warning" style="width: {{ $satisfactionRate ?? 0 }}%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Pending Prestataires -->
    <div class="table-card">
        <div class="table-header">
            <div class="table-title">Prestataires en Attente</div>
            <a href="{{ route('administrateur.prestataires.pending') }}" class="btn btn-outline">
                <i class="fas fa-eye"></i>
                Voir tout
            </a>
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPrestataires ?? [] as $prestataire)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                                        @if($prestataire->photo)
                                            <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="{{ $prestataire->user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @elseif($prestataire->user->avatar)
                                            <img src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="{{ $prestataire->user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <div style="width: 100%; height: 100%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                                {{ substr($prestataire->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        @if($prestataire->isVerified())
                                            <div style="position: absolute; top: -2px; right: -2px; width: 12px; height: 12px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid white;">
                                                <svg style="width: 8px; height: 8px; color: white;" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                                            {{ $prestataire->user->name }}
                                            @if($prestataire->isVerified())
                                                <span style="padding: 0.125rem 0.375rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 500; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                                    ✓ Vérifié
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.875rem; color: var(--secondary);">{{ $prestataire->secteur_activite ?? 'Non spécifié' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $prestataire->user->email }}</td>
                            <td>
                                <a href="{{ route('administrateur.prestataires.show', $prestataire->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">
                                    <i class="fas fa-eye"></i>
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem; color: var(--secondary);">
                                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--success);"></i>
                                <div>Aucun prestataire en attente</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Recent Users -->
    <div class="table-card">
        <div class="table-header">
            <div class="table-title">Derniers Utilisateurs</div>
            <a href="{{ route('administrateur.users.index') }}" class="btn btn-outline">
                <i class="fas fa-eye"></i>
                Voir tout
            </a>
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Inscrit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers ?? [] as $user)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; background: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 500;">{{ $user->name }}</div>
                                        <div style="font-size: 0.875rem; color: var(--secondary);">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="padding: 0.25rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500; 
                                    @if($user->role === 'administrateur') background: rgba(79, 70, 229, 0.1); color: var(--primary);
                                    @elseif($user->role === 'prestataire') background: rgba(16, 185, 129, 0.1); color: var(--success);
                                    @else background: rgba(107, 114, 128, 0.1); color: var(--secondary); @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td style="color: var(--secondary); font-size: 0.875rem;">
                                {{ $user->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem; color: var(--secondary);">
                                <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                <div>Aucun utilisateur récent</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('registrationsChart').getContext('2d');
        let chart;

        const initialData = @json($chartData);

        function renderChart(data) {
            if (chart) {
                chart.destroy();
            }
            chart = new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }

        function updateChart(period) {
            fetch(`{{ route('administrateur.dashboard.chart') }}?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    renderChart(data);
                });
        }

        document.querySelectorAll('#chart-period-buttons button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('#chart-period-buttons button').forEach(btn => btn.classList.remove('btn-primary'));
                this.classList.add('btn-primary');
                updateChart(this.dataset.period);
            });
        });

        // Initial render
        renderChart(initialData);

        // Animate counters on page load
        const counters = document.querySelectorAll('.stat-value');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            if (isNaN(target)) return;
            const increment = target > 0 ? target / 100 : 0;
            let current = 0;
            
            if (target === 0) {
                counter.textContent = 0;
                return;
            }

            const timer = setInterval(() => {
                current += increment;
                counter.textContent = Math.floor(current);
                
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                }
            }, 20);
        });
    });
</script>
@endpush
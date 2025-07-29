<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title') - Administration TaPrestation</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom styles -->
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --secondary: #6b7280;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --light: #f8fafc;
            --dark: #1f2937;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            line-height: 1.6;
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, var(--sidebar-bg) 0%, #0f172a 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .sidebar-brand-icon {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .sidebar-brand-text {
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-section {
            margin-bottom: 2rem;
        }
        
        .nav-section-title {
            padding: 0 1.5rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
        }
        
        .nav-item {
            margin: 0.25rem 1rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: white;
            transform: translateX(4px);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }
        
        .badge-notification {
            background: var(--danger);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            margin-left: auto;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        .header {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .header-left h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }
        
        .header-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f8fafc;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .user-menu:hover {
            background: #e2e8f0;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        /* Content Area */
        .content {
            padding: 2rem;
            flex: 1;
        }
        
        /* Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card.primary {
            border-left-color: var(--primary);
        }
        
        .stat-card.success {
            border-left-color: var(--success);
        }
        
        .stat-card.warning {
            border-left-color: var(--warning);
        }
        
        .stat-card.info {
            border-left-color: var(--info);
        }
        
        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .stat-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-icon.primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        
        .stat-icon.success {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        }
        
        .stat-icon.warning {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
        }
        
        .stat-icon.info {
            background: linear-gradient(135deg, var(--info) 0%, #0891b2 100%);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .stat-change {
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .stat-change.positive {
            color: var(--success);
        }
        
        .stat-change.negative {
            color: var(--danger);
        }
        
        /* Chart Cards */
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        /* Tables */
        .table-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .table-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .modern-table th {
            background: #f8fafc;
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .modern-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        
        .modern-table tbody tr:hover {
            background: #f8fafc;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-warning {
            background: var(--warning);
            color: white;
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #e2e8f0;
            color: var(--secondary);
        }
        
        .btn-outline:hover {
            background: #f8fafc;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 1rem;
            }
        }
        
        /* Progress bars */
        .progress {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin: 0.5rem 0;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .progress-bar.primary {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        
        .progress-bar.success {
            background: linear-gradient(90deg, var(--success) 0%, #059669 100%);
        }
        
        .progress-bar.warning {
            background: linear-gradient(90deg, var(--warning) 0%, #d97706 100%);
        }
        
        .progress-bar.info {
            background: linear-gradient(90deg, var(--info) 0%, #0891b2 100%);
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="sidebar-brand-text">TaPrestation</div>
            </div>
            
            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.dashboard') ? 'active' : '' }}" href="{{ route('administrateur.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Tableau de bord</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Gestion des utilisateurs</div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.users.*') ? 'active' : '' }}" href="{{ route('administrateur.users.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Utilisateurs</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.prestataires.*') ? 'active' : '' }}" href="{{ route('administrateur.prestataires.index') }}">
                            <i class="fas fa-user-tie"></i>
                            <span>Prestataires</span>
                            @php
                                $pendingCount = \App\Models\Prestataire::where('is_approved', false)->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="badge-notification">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.verifications.index') ? 'active' : '' }}" href="{{ route('admin.verifications.index') }}">
                            <i class="bi bi-patch-check-fill"></i>
                            <span>Vérifications</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.clients.*') ? 'active' : '' }}" href="{{ route('administrateur.clients.index') }}">
                            <i class="fas fa-user"></i>
                            <span>Clients</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Gestion du contenu</div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.categories.*') ? 'active' : '' }}" href="{{ route('administrateur.categories.index') }}">
                            <i class="fas fa-folder"></i>
                            <span>Catégories</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.skills.*') ? 'active' : '' }}" href="{{ route('administrateur.skills.index') }}">
                            <i class="fas fa-tools"></i>
                            <span>Compétences</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.services.*') ? 'active' : '' }}" href="{{ route('administrateur.services.index') }}">
                            <i class="fas fa-briefcase"></i>
                            <span>Services</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.reviews.*') ? 'active' : '' }}" href="{{ route('administrateur.reviews.index') }}">
                            <i class="fas fa-star"></i>
                            <span>Avis</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Gestion des activités</div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.bookings.*') ? 'active' : '' }}" href="{{ route('administrateur.bookings.index') }}">
                            <i class="fas fa-calendar-check"></i>
                            <span>Réservations</span>
                            @php
                                $pendingBookingsCount = \App\Models\Booking::where('status', 'pending')->count();
                            @endphp
                            @if($pendingBookingsCount > 0)
                                <span class="badge-notification">{{ $pendingBookingsCount }}</span>
                            @endif
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.client-requests.*') ? 'active' : '' }}" href="{{ route('administrateur.client-requests.index') }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Demandes clients</span>
                            @php
                                $activeRequestsCount = \App\Models\ClientRequest::where('status', 'active')->count();
                            @endphp
                            @if($activeRequestsCount > 0)
                                <span class="badge-notification">{{ $activeRequestsCount }}</span>
                            @endif
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.offers.*') ? 'active' : '' }}" href="{{ route('administrateur.offers.index') }}">
                            <i class="fas fa-handshake"></i>
                            <span>Offres</span>
                            @php
                                $pendingOffersCount = \App\Models\Offer::where('status', 'pending')->count();
                            @endphp
                            @if($pendingOffersCount > 0)
                                <span class="badge-notification">{{ $pendingOffersCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Communication</div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.notifications.*') ? 'active' : '' }}" href="{{ route('administrateur.notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                            @php
                                $unreadNotificationsCount = \App\Models\Notification::where('read_at', null)->count();
                            @endphp
                            @if($unreadNotificationsCount > 0)
                                <span class="badge-notification">{{ $unreadNotificationsCount }}</span>
                            @endif
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.messages.*') ? 'active' : '' }}" href="{{ route('administrateur.messages.index') }}">
                            <i class="fas fa-comments"></i>
                            <span>Messages</span>
                            @php
                                $reportedMessagesCount = \App\Models\Message::where('is_reported', true)->where('status', '!=', 'hidden')->count();
                            @endphp
                            @if($reportedMessagesCount > 0)
                                <span class="badge-notification">{{ $reportedMessagesCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Modération</div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.reports.urgent-sales.*') ? 'active' : '' }}" href="{{ route('administrateur.reports.urgent-sales.index') }}">
                            <i class="fas fa-flag"></i>
                            <span>Signalements Ventes</span>
                            @php
                                $pendingUrgentSaleReports = \App\Models\UrgentSaleReport::where('status', 'pending')->count();
                            @endphp
                            @if($pendingUrgentSaleReports > 0)
                                <span class="badge-notification">{{ $pendingUrgentSaleReports }}</span>
                            @endif
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.reports.equipments.*') ? 'active' : '' }}" href="{{ route('administrateur.reports.equipments.index') }}">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Signalements Équipements</span>
                            @php
                                $pendingEquipmentReports = \App\Models\EquipmentReport::where('status', 'pending')->count();
                            @endphp
                            @if($pendingEquipmentReports > 0)
                                <span class="badge-notification">{{ $pendingEquipmentReports }}</span>
                            @endif
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.reports.all.*') ? 'active' : '' }}" href="{{ route('administrateur.reports.all.index') }}">
                            <i class="fas fa-shield-alt"></i>
                            <span>Tous les signalements</span>
                            @php
                                $totalPendingReports = \App\Models\UrgentSaleReport::where('status', 'pending')->count() + \App\Models\EquipmentReport::where('status', 'pending')->count();
                            @endphp
                            @if($totalPendingReports > 0)
                                <span class="badge-notification">{{ $totalPendingReports }}</span>
                            @endif
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Analyses & Rapports</div>
                    
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('administrateur.analytics.*') ? 'active' : '' }}" href="{{ route('administrateur.analytics.dashboard') }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Rapports</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <h1>@yield('page-title', 'Administration')</h1>
                </div>
                <div class="header-right">
                    <div class="user-menu">
                        <div class="user-avatar">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
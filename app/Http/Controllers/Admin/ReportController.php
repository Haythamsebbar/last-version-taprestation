<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Prestataire;
use App\Models\Client;
use App\Models\Service;
use App\Models\Booking;
use App\Models\ClientRequest;
use App\Models\Offer;
use App\Models\Review;
use App\Models\Message;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Affiche le tableau de bord des rapports.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Statistiques générales
        $generalStats = [
            'total_users' => User::count(),
            'total_prestataires' => Prestataire::count(),
            'total_clients' => Client::count(),
            'total_services' => Service::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::where('status', 'completed')->sum('total_price'),
        ];
        
        // Évolution mensuelle
        $monthlyEvolution = $this->getMonthlyEvolution();
        
        // Top catégories
        $topCategories = $this->getTopCategories();
        
        // Statistiques de conversion
        $conversionStats = $this->getConversionStats();
        
        return view('admin.reports.index', [
            'generalStats' => $generalStats,
            'monthlyEvolution' => $monthlyEvolution,
            'topCategories' => $topCategories,
            'conversionStats' => $conversionStats,
        ]);
    }
    
    /**
     * Affiche le tableau de bord principal des rapports.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Statistiques générales
        $generalStats = [
            'total_users' => User::count(),
            'total_prestataires' => Prestataire::count(),
            'total_clients' => Client::count(),
            'total_services' => Service::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::where('status', 'completed')->sum('total_price'),
        ];
        
        // Évolution mensuelle
        $monthlyEvolution = $this->getMonthlyEvolution();
        
        // Top catégories
        $topCategories = $this->getTopCategories();
        
        // Statistiques de conversion
        $conversionStats = $this->getConversionStats();
        
        return view('admin.reports.dashboard', [
            'generalStats' => $generalStats,
            'monthlyEvolution' => $monthlyEvolution,
            'topCategories' => $topCategories,
            'conversionStats' => $conversionStats,
        ]);
    }
    
    /**
     * Rapport des utilisateurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function users(Request $request)
    {
        $period = $request->get('period', '30'); // Derniers 30 jours par défaut
        $startDate = now()->subDays($period);
        
        // Nouvelles inscriptions
        $newUsers = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Répartition par rôle
        $usersByRole = [
            'clients' => Client::whereHas('user', function($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            })->count(),
            'prestataires' => Prestataire::whereHas('user', function($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            })->count(),
        ];
        
        // Utilisateurs actifs
        $activeUsers = User::where('last_login_at', '>=', $startDate)->count();
        
        // Top villes
        $topCities = User::select('city', DB::raw('count(*) as count'))
            ->whereNotNull('city')
            ->where('created_at', '>=', $startDate)
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.reports.users', [
            'newUsers' => $newUsers,
            'usersByRole' => $usersByRole,
            'activeUsers' => $activeUsers,
            'topCities' => $topCities,
            'period' => $period,
        ]);
    }
    
    /**
     * Rapport des services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function services(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);
        
        // Services par catégorie
        $servicesByCategory = Service::join('categories', 'services.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->where('services.created_at', '>=', $startDate)
            ->groupBy('categories.name')
            ->orderBy('count', 'desc')
            ->get();
        
        // Services les plus populaires
        $popularServices = Service::withCount(['bookings' => function($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();
        
        // Prix moyens par catégorie
        $avgPricesByCategory = Service::join('categories', 'services.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('AVG(services.price) as avg_price'))
            ->where('services.created_at', '>=', $startDate)
            ->groupBy('categories.name')
            ->get();
        
        // Évolution des créations de services
        $serviceCreation = Service::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return view('admin.reports.services', [
            'servicesByCategory' => $servicesByCategory,
            'popularServices' => $popularServices,
            'avgPricesByCategory' => $avgPricesByCategory,
            'serviceCreation' => $serviceCreation,
            'period' => $period,
        ]);
    }
    
    /**
     * Rapport des réservations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function bookings(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);
        
        // Réservations par statut
        $bookingsByStatus = Booking::where('created_at', '>=', $startDate)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
        
        // Évolution des réservations
        $bookingEvolution = Booking::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_price) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Revenus par mois
        $monthlyRevenue = Booking::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_price) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Top prestataires par revenus
        $topPrestataires = Prestataire::join('services', 'prestataires.id', '=', 'services.prestataire_id')
            ->join('bookings', 'services.id', '=', 'bookings.service_id')
            ->join('users', 'prestataires.user_id', '=', 'users.id')
            ->where('bookings.status', 'completed')
            ->where('bookings.created_at', '>=', $startDate)
            ->select('users.name', DB::raw('SUM(bookings.total_price) as total_revenue'))
            ->groupBy('prestataires.id', 'users.name')
            ->orderBy('total_revenue', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.reports.bookings', [
            'bookingsByStatus' => $bookingsByStatus,
            'bookingEvolution' => $bookingEvolution,
            'monthlyRevenue' => $monthlyRevenue,
            'topPrestataires' => $topPrestataires,
            'period' => $period,
        ]);
    }
    
    /**
     * Rapport financier.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function financial(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);
        
        // Revenus totaux
        $totalRevenue = Booking::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('total_price');
        
        // Commission de la plateforme (supposons 10%)
        $platformCommission = $totalRevenue * 0.10;
        
        // Revenus par catégorie
        $revenueByCategory = Booking::join('services', 'bookings.service_id', '=', 'services.id')
            ->join('categories', 'services.category_id', '=', 'categories.id')
            ->where('bookings.status', 'completed')
            ->where('bookings.created_at', '>=', $startDate)
            ->select('categories.name', DB::raw('SUM(bookings.total_price) as revenue'))
            ->groupBy('categories.name')
            ->orderBy('revenue', 'desc')
            ->get();
        
        // Évolution quotidienne des revenus
        $dailyRevenue = Booking::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Panier moyen
        $averageOrderValue = Booking::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->avg('total_price');
        
        return view('admin.reports.financial', [
            'totalRevenue' => $totalRevenue,
            'platformCommission' => $platformCommission,
            'revenueByCategory' => $revenueByCategory,
            'dailyRevenue' => $dailyRevenue,
            'averageOrderValue' => $averageOrderValue,
            'period' => $period,
        ]);
    }
    
    /**
     * Export d'un rapport.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $type)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);
        
        $filename = "rapport_{$type}_" . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($type, $startDate) {
            $file = fopen('php://output', 'w');
            
            switch ($type) {
                case 'users':
                    $this->exportUsersReport($file, $startDate);
                    break;
                case 'services':
                    $this->exportServicesReport($file, $startDate);
                    break;
                case 'bookings':
                    $this->exportBookingsReport($file, $startDate);
                    break;
                case 'financial':
                    $this->exportFinancialReport($file, $startDate);
                    break;
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Obtient l'évolution mensuelle.
     *
     * @return array
     */
    private function getMonthlyEvolution()
    {
        $evolution = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $evolution[] = [
                'month' => $date->format('M Y'),
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'bookings' => Booking::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'revenue' => Booking::where('status', 'completed')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_price'),
            ];
        }
        
        return $evolution;
    }
    
    /**
     * Obtient les top catégories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTopCategories()
    {
        return Category::withCount('services')
            ->orderBy('services_count', 'desc')
            ->take(5)
            ->get();
    }
    
    /**
     * Obtient les statistiques de conversion.
     *
     * @return array
     */
    private function getConversionStats()
    {
        $totalRequests = ClientRequest::count();
        $totalOffers = Offer::count();
        $acceptedOffers = Offer::where('status', 'accepted')->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        
        return [
            'request_to_offer' => $totalRequests > 0 ? round(($totalOffers / $totalRequests) * 100, 1) : 0,
            'offer_to_acceptance' => $totalOffers > 0 ? round(($acceptedOffers / $totalOffers) * 100, 1) : 0,
            'booking_completion' => Booking::count() > 0 ? round(($completedBookings / Booking::count()) * 100, 1) : 0,
        ];
    }
    
    /**
     * Exporte le rapport des utilisateurs.
     *
     * @param  resource  $file
     * @param  \Carbon\Carbon  $startDate
     */
    private function exportUsersReport($file, $startDate)
    {
        fputcsv($file, ['Date', 'Nouveaux Utilisateurs', 'Nouveaux Clients', 'Nouveaux Prestataires']);
        
        $data = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        foreach ($data as $row) {
            $clients = Client::whereHas('user', function($q) use ($row) {
                $q->whereDate('created_at', $row->date);
            })->count();
            
            $prestataires = Prestataire::whereHas('user', function($q) use ($row) {
                $q->whereDate('created_at', $row->date);
            })->count();
            
            fputcsv($file, [$row->date, $row->total, $clients, $prestataires]);
        }
    }
    
    /**
     * Exporte le rapport des services.
     *
     * @param  resource  $file
     * @param  \Carbon\Carbon  $startDate
     */
    private function exportServicesReport($file, $startDate)
    {
        fputcsv($file, ['Catégorie', 'Nombre de Services', 'Prix Moyen']);
        
        $data = Service::join('categories', 'services.category_id', '=', 'categories.id')
            ->where('services.created_at', '>=', $startDate)
            ->select('categories.name', DB::raw('COUNT(*) as count'), DB::raw('AVG(services.price) as avg_price'))
            ->groupBy('categories.name')
            ->get();
        
        foreach ($data as $row) {
            fputcsv($file, [$row->name, $row->count, round($row->avg_price, 2)]);
        }
    }
    
    /**
     * Exporte le rapport des réservations.
     *
     * @param  resource  $file
     * @param  \Carbon\Carbon  $startDate
     */
    private function exportBookingsReport($file, $startDate)
    {
        fputcsv($file, ['Date', 'Nombre de Réservations', 'Revenus']);
        
        $data = Booking::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_price) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        foreach ($data as $row) {
            fputcsv($file, [$row->date, $row->count, round($row->revenue, 2)]);
        }
    }
    
    /**
     * Exporte le rapport financier.
     *
     * @param  resource  $file
     * @param  \Carbon\Carbon  $startDate
     */
    private function exportFinancialReport($file, $startDate)
    {
        fputcsv($file, ['Catégorie', 'Revenus', 'Commission Plateforme (10%)']);
        
        $data = Booking::join('services', 'bookings.service_id', '=', 'services.id')
            ->join('categories', 'services.category_id', '=', 'categories.id')
            ->where('bookings.status', 'completed')
            ->where('bookings.created_at', '>=', $startDate)
            ->select('categories.name', DB::raw('SUM(bookings.total_price) as revenue'))
            ->groupBy('categories.name')
            ->get();
        
        foreach ($data as $row) {
            $commission = $row->revenue * 0.10;
            fputcsv($file, [$row->name, round($row->revenue, 2), round($commission, 2)]);
        }
    }
}
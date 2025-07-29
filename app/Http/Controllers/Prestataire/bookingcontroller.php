<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\EquipmentRentalRequest;
use App\Models\UrgentSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Affiche la liste unifiée des réservations, demandes de location et ventes urgentes
     */
    public function index(Request $request)
    {
        $prestataire = Auth::user()->prestataire;
        $type = $request->get('type', 'all');
        
        $bookings = collect();
        $equipmentRentalRequests = collect();
        $urgentSales = collect();
        
        // Récupérer les réservations de services
        if ($type === 'all' || $type === 'bookings') {
            $bookings = $prestataire->bookings()
                ->with(['service', 'client.user'])
                ->latest()
                ->get();
        }
        
        // Récupérer les demandes de location d'équipement
        if ($type === 'all' || $type === 'equipment') {
            $equipmentRentalRequests = $prestataire->equipmentRentalRequests()
                ->with(['equipment', 'client.user'])
                ->latest()
                ->get();
        }
        
        // Récupérer les ventes urgentes
        if ($type === 'all' || $type === 'urgent_sales') {
            $urgentSales = $prestataire->urgentSales()
                ->with(['client.user'])
                ->latest()
                ->get();
        }
        
        // Statistiques
        $stats = [
            'total_bookings' => $prestataire->bookings()->count(),
            'pending_bookings' => $prestataire->bookings()->where('status', 'pending')->count(),
            'confirmed_bookings' => $prestataire->bookings()->where('status', 'confirmed')->count(),
            'total_equipment_requests' => $prestataire->equipmentRentalRequests()->count(),
            'pending_equipment_requests' => $prestataire->equipmentRentalRequests()->where('status', 'pending')->count(),
            'accepted_equipment_requests' => $prestataire->equipmentRentalRequests()->where('status', 'accepted')->count(),
            'total_urgent_sales' => $prestataire->urgentSales()->count(),
            'pending_urgent_sales' => $prestataire->urgentSales()->where('status', 'pending')->count(),
        ];
        
        return view('prestataire.bookings.index', compact(
            'bookings', 
            'equipmentRentalRequests', 
            'urgentSales', 
            'stats', 
            'type'
        ));
    }
    
    /**
     * Affiche les détails d'une réservation
     */
    public function show(Booking $booking)
    {
        $booking->load(['service', 'client.user', 'prestataire']);
        
        return view('prestataire.bookings.show', compact('booking'));
    }
}
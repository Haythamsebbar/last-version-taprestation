<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Carbon\Carbon;

class AgendaController extends Controller
{
    /**
     * Affiche l'agenda du prestataire
     */
    public function index()
    {
        return view('prestataires.agenda.index');
    }
    
    /**
     * API pour récupérer les événements du calendrier
     */
    public function events(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        $start = Carbon::parse($request->get('start'));
        $end = Carbon::parse($request->get('end'));
        
        $bookings = Booking::where('prestataire_id', $prestataire->id)
            ->whereBetween('start_datetime', [$start, $end])
            ->with(['service', 'client.user'])
            ->get();
        
        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->service->title ?? 'Réservation',
                'start' => $booking->start_datetime->toISOString(),
                'end' => $booking->end_datetime->toISOString(),
                'backgroundColor' => $this->getStatusColor($booking->status),
                'borderColor' => $this->getStatusColor($booking->status),
                'extendedProps' => [
                    'clientName' => $booking->client->user->name ?? 'N/A',
                    'serviceName' => $booking->service->title ?? 'N/A',
                    'status' => ucfirst($booking->status),
                    'bookingUrl' => route('prestataire.bookings.show', $booking->id),
                    'startTime' => $booking->start_datetime->format('H:i')
                ]
            ];
        });
        
        return response()->json($events);
    }
    
    /**
     * Affiche les détails d'une réservation
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();
        
        // Vérifier que la réservation appartient au prestataire connecté
        if ($booking->prestataire_id !== $user->prestataire->id) {
            abort(403);
        }
        
        $booking->load(['service', 'client.user', 'timeSlot']);
        
        return response()->json([
            'booking' => $booking,
            'canConfirm' => $booking->canBeConfirmed(),
            'canCancel' => $booking->canBeCancelled(),
            'canComplete' => $booking->canBeCompleted()
        ]);
    }
    
    /**
     * Met à jour le statut d'une réservation
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $user = Auth::user();
        
        // Vérifier que la réservation appartient au prestataire connecté
        if ($booking->prestataire_id !== $user->prestataire->id) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed',
            'reason' => 'nullable|string|max:500'
        ]);
        
        $status = $request->get('status');
        $reason = $request->get('reason');
        
        switch ($status) {
            case 'confirmed':
                if ($booking->confirm()) {
                    return response()->json(['success' => true, 'message' => 'Réservation confirmée']);
                }
                break;
                
            case 'cancelled':
                if ($booking->cancel($reason)) {
                    return response()->json(['success' => true, 'message' => 'Réservation annulée']);
                }
                break;
                
            case 'completed':
                if ($booking->canBeCompleted()) {
                    $booking->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);
                    return response()->json(['success' => true, 'message' => 'Réservation marquée comme terminée']);
                }
                break;
        }
        
        return response()->json(['success' => false, 'message' => 'Action non autorisée'], 400);
    }
    

    
    /**
     * Retourne la couleur selon le statut
     */
    public function recentBookings(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;

        $bookings = Booking::where('prestataire_id', $prestataire->id)
            ->with(['service', 'client.user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json($bookings);
    }

    /**
     * Retourne la couleur selon le statut
     */
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => '#ff9f43',
            'confirmed' => '#28c76f',
            'cancelled' => '#82868b',
            'completed' => '#1e90ff',
            'refused' => '#dc3545' // Red
        ];
        
        return $colors[$status] ?? '#82868b';
    }
}
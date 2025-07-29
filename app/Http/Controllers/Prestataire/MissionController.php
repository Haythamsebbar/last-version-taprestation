<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\ClientRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MissionCompletedNotification;

class MissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:prestataire']);
    }

    public function index()
    {
        $prestataire = Auth::user()->prestataire;

        $missions = ClientRequest::with('client.user')->whereHas('offers', function($query) use ($prestataire) {
            $query->where('prestataire_id', $prestataire->id)
                  ->where('status', 'accepted');
        })
        ->whereIn('status', ['in_progress', 'pending'])
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('prestataire.missions.index', compact('missions'));
    }

    public function confirmCompletion(ClientRequest $request)
    {
        $prestataire = Auth::user()->prestataire;

        // Find the accepted offer for this request made by the current prestataire
        $offer = $request->offers()->where('prestataire_id', $prestataire->id)->where('status', 'accepted')->first();

        if (!$offer) {
            return redirect()->back()->with('error', 'Offre non trouvée ou non acceptée.');
        }

        if ($request->status !== 'completed_by_client') {
            return redirect()->back()->with('error', 'Cette mission ne peut pas être marquée comme terminée.');
        }

        // Update request status
        $request->status = 'completed';
        $request->completed_at = now();
        $request->save();

        // Update booking status
        $booking = Booking::where('offer_id', $offer->id)->first();
        if ($booking) {
            $booking->status = 'completed';
            $booking->completed_at = now();
            $booking->save();
        }

        // Notify client
        Notification::send($request->client, new MissionCompletedNotification($request, $booking));

        return redirect()->back()->with('success', 'Mission terminée avec succès.');
    }
}
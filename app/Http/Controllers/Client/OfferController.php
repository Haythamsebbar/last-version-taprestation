<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Offer;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OfferAcceptedNotification;
use App\Notifications\OfferRejectedNotification;

class OfferController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:client']);
    }

    /**
     * Affiche la liste des offres reçues.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $client = Auth::user();
        
        // Récupérer toutes les offres pour les demandes du client
        $offers = Offer::where('client_id', $client->id)
        ->with(['prestataire.user'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        
        // Statistiques des offres
        $pendingOffers = Offer::where('client_id', $client->id)->where('status', 'pending')->get();
        
        $acceptedOffers = Offer::where('client_id', $client->id)->where('status', 'accepted')->get();
        
        $rejectedOffers = Offer::where('client_id', $client->id)->where('status', 'rejected')->get();
        
        return view('client.offers.index', compact(
            'offers',
            'pendingOffers',
            'acceptedOffers',
            'rejectedOffers'
        ));
    }

    /**
     * Accepte une offre.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept($id)
    {
        $offer = Offer::findOrFail($id);
        
        // Vérifier que l'offre est en attente
        if ($offer->status !== 'pending') {
            return redirect()->route('client.offers.index')
                ->with('error', 'Cette offre a déjà été traitée.');
        }
        
        // Mettre à jour le statut de l'offre
        $offer->status = 'accepted';
        $offer->save();
        
        // Créer une réservation
        $booking = new Booking();
        $booking->booking_number = 'BK' . now()->format('YmdHis') . $offer->id;
        $booking->client_id = Auth::id();
        $booking->prestataire_id = $offer->prestataire_id;
        $booking->service_id = null; // Pas de service spécifique pour une offre personnalisée
        $booking->total_price = $offer->price;
        $booking->status = 'confirmed';
        $booking->client_notes = "Offre acceptée";
        $booking->offer_id = $offer->id;
        $booking->save();
        
        // Notifier le prestataire
        $prestataire = $offer->prestataire;
        if ($prestataire && $prestataire->user) {
            $clientRequest = $offer->clientRequest;
            Notification::send($prestataire->user, new OfferAcceptedNotification($offer, $clientRequest, $booking));
        }
        
        return redirect()->route('client.offers.index')
            ->with('success', 'Vous avez accepté l\'offre avec succès. Une réservation a été créée.');
    }

    /**
     * Rejette une offre.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $offer = Offer::findOrFail($id);
        
        // Vérifier que l'offre est en attente
        if ($offer->status !== 'pending') {
            return redirect()->route('client.offers.index')
                ->with('error', 'Cette offre a déjà été traitée.');
        }
        
        // Mettre à jour le statut de l'offre
        $offer->status = 'rejected';
        $offer->save();
        
        // Notifier le prestataire
        $prestataire = $offer->prestataire;
        if ($prestataire && $prestataire->user) {
            $clientRequest = $offer->clientRequest;
            Notification::send($prestataire->user, new OfferRejectedNotification($offer, $clientRequest));
        }
        
        return redirect()->route('client.offers.index')
            ->with('success', 'Vous avez refusé l\'offre avec succès.');
    }
}
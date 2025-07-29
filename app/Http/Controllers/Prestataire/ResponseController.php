<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:prestataire']);
    }

    /**
     * Display a listing of the prestataire's responses/offers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get the authenticated prestataire
        $user = Auth::user();
        $prestataire = $user->prestataire;

        // Ensure the prestataire is validated
        if (!$prestataire || !$prestataire->is_approved) {
            return redirect()->route('prestataire.dashboard')
                ->with('error', 'Vous devez être un prestataire validé pour accéder à cette section.');
        }

        // Get all offers made by the prestataire
        $query = Offer::where('prestataire_id', $user->id)
            ->with(['clientRequest' => function($query) {
                $query->with('client');
            }]);

        // Apply filters if provided
        if ($request->has('status')) {
            $status = $request->input('status');
            if (!empty($status)) {
                $query->where('status', $status);
            }
        }

        if ($request->has('date_from')) {
            $dateFrom = $request->input('date_from');
            if (!empty($dateFrom)) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
        }

        if ($request->has('date_to')) {
            $dateTo = $request->input('date_to');
            if (!empty($dateTo)) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
        }

        // Sort by date (default: newest first)
        $sortBy = $request->input('sort_by', 'newest');
        if ($sortBy === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        // Get the filtered offers
        $offers = $query->paginate(10);

        return view('prestataire.responses.index', compact('offers'));
    }

    /**
     * Display the specified offer details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get the authenticated prestataire
        $user = Auth::user();

        // Find the offer and ensure it belongs to the prestataire
        $offer = Offer::where('id', $id)
            ->where('prestataire_id', $user->id)
            ->with(['clientRequest' => function($query) {
                $query->with('client');
            }])
            ->firstOrFail();

        return view('prestataire.responses.show', compact('offer'));
    }

    /**
     * Update the specified offer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Get the authenticated prestataire
        $user = Auth::user();

        // Find the offer and ensure it belongs to the prestataire
        $offer = Offer::where('id', $id)
            ->where('prestataire_id', $user->id)
            ->firstOrFail();

        // Only allow updates if the offer is still pending
        if ($offer->status !== 'pending') {
            return redirect()->route('prestataire.responses.index')
                ->with('error', 'Vous ne pouvez pas modifier une offre qui a déjà été acceptée ou refusée.');
        }

        // Validate the request
        $validatedData = $request->validate([
            'message' => 'required|string|max:1000',
            // 'price' => 'required|numeric|min:0', // Supprimé pour des raisons de confidentialité
        ]);

        // Update the offer
        $offer->message = $validatedData['message'];
        // $offer->price = $validatedData['price']; // Supprimé pour des raisons de confidentialité
        $offer->save();

        return redirect()->route('prestataire.responses.index')
            ->with('success', 'Votre offre a été mise à jour avec succès.');
    }

    /**
     * Cancel the specified offer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        // Get the authenticated prestataire
        $user = Auth::user();

        // Find the offer and ensure it belongs to the prestataire
        $offer = Offer::where('id', $id)
            ->where('prestataire_id', $user->id)
            ->firstOrFail();

        // Only allow cancellation if the offer is still pending
        if ($offer->status !== 'pending') {
            return redirect()->route('prestataire.responses.index')
                ->with('error', 'Vous ne pouvez pas annuler une offre qui a déjà été acceptée ou refusée.');
        }

        // Delete the offer
        $offer->delete();

        // Check if there are no more offers for this request, update status back to pending
        $clientRequest = ClientRequest::find($offer->client_request_id);
        if ($clientRequest && $clientRequest->offers()->count() === 0) {
            $clientRequest->status = 'pending';
            $clientRequest->save();
        }

        return redirect()->route('prestataire.responses.index')
            ->with('success', 'Votre offre a été annulée avec succès.');
    }
}
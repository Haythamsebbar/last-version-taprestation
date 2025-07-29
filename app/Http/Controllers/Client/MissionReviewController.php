<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientRequest;
use App\Models\Review;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewReviewNotification;

class MissionReviewController extends Controller
{
    /**
     * Affiche le formulaire d'évaluation pour une mission terminée
     */
    public function create(Request $request)
    {
        $clientRequest = ClientRequest::findOrFail($request->clientRequest);
        
        // Vérifier que la demande est terminée
        if ($clientRequest->status !== 'completed') {
            return redirect()->back()->with('error', 'Vous ne pouvez évaluer que des missions terminées.');
        }
        
        // Vérifier que le client est bien le propriétaire de la demande
        if ($clientRequest->client_id !== Auth::id()) {
            return abort(403, 'Vous n\'êtes pas autorisé à évaluer cette mission.');
        }
        
        // Récupérer l'offre acceptée pour cette demande
        $acceptedOffer = $clientRequest->offers()->where('status', 'accepted')->first();
        
        if (!$acceptedOffer) {
            return redirect()->back()->with('error', 'Aucune offre acceptée trouvée pour cette mission.');
        }
        
        // Vérifier si une évaluation existe déjà
        $existingReview = Review::where([
            'client_id' => Auth::id(),
            'prestataire_id' => $acceptedOffer->prestataire_id,
            'booking_id' => $clientRequest->booking ? $clientRequest->booking->id : null
        ])->first();
        
        if ($existingReview) {
            return redirect()->back()->with('info', 'Vous avez déjà évalué cette mission.');
        }
        
        return view('client.reviews.create', [
            'clientRequest' => $clientRequest,
            'prestataire' => $acceptedOffer->prestataire
        ]);
    }
    
    /**
     * Enregistre une nouvelle évaluation
     */
    public function store(Request $request)
    {
        $clientRequest = ClientRequest::findOrFail($request->clientRequest);
        
        // Validation des données
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:30|max:1000',
            'prestataire_id' => 'required|exists:prestataires,id',
        ], [
            'rating.required' => 'La note est obligatoire.',
            'rating.min' => 'La note doit être au minimum de 1 étoile.',
            'rating.max' => 'La note doit être au maximum de 5 étoiles.',
            'comment.required' => 'Le commentaire est obligatoire.',
            'comment.min' => 'Le commentaire doit contenir au moins 30 caractères.',
            'comment.max' => 'Le commentaire ne doit pas dépasser 1000 caractères.',
        ]);
        
        // Vérifier que la demande est terminée
        if ($clientRequest->status !== 'completed') {
            return redirect()->back()->with('error', 'Vous ne pouvez évaluer que des missions terminées.');
        }
        
        // Vérifier que le client est bien le propriétaire de la demande
        if ($clientRequest->client_id !== Auth::id()) {
            return abort(403, 'Vous n\'êtes pas autorisé à évaluer cette mission.');
        }
        
        // Vérifier si une évaluation existe déjà
        $existingReview = Review::where([
            'client_id' => Auth::id(),
            'prestataire_id' => $validated['prestataire_id'],
            'booking_id' => $clientRequest->booking ? $clientRequest->booking->id : null
        ])->first();
        
        if ($existingReview) {
            return redirect()->back()->with('info', 'Vous avez déjà évalué cette mission.');
        }
        
        // Créer la nouvelle évaluation
        $review = Review::create([
            'client_id' => Auth::user()->client->id,
            'prestataire_id' => $validated['prestataire_id'],
            'booking_id' => $clientRequest->booking->id ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'verified' => true, // L'évaluation est vérifiée car liée à une mission terminée
            'status' => 'pending', // En attente de modération
        ]);
        
        // Envoyer une notification au prestataire
        $prestataire = $review->prestataire;
        if ($prestataire && $prestataire->user) {
            Notification::send($prestataire->user, new NewReviewNotification($review));
        }
        
        return redirect()->route('client.requests.show', $clientRequest)
            ->with('success', 'Votre évaluation a été enregistrée avec succès. Merci pour votre retour !');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index(Request $request): View
    {
        // Default to null if no prestataire ID is provided
        $prestataireId = $request->query('prestataire_id');
        
        $reviews = Review::with(['client.user', 'prestataire.user']);
        
        // Filter by prestataire if ID is provided
        if ($prestataireId) {
            $reviews->where('prestataire_id', $prestataireId);
        }
        
        $reviews = $reviews->latest()->paginate(10);
        
        // Statistiques pour les avis
        $stats = [
            'total_reviews' => Review::count(),
            'average_rating' => Review::avg('rating') ?: 0,
            'reviews_with_photos' => Review::whereRaw("JSON_LENGTH(IFNULL(photos, '[]')) > 0")->count(),
            'detailed_averages' => [
                'punctuality' => Review::avg('punctuality_rating') ?: 0,
                'quality' => Review::avg('quality_rating') ?: 0,
                'value' => Review::avg('value_rating') ?: 0,
                'communication' => Review::avg('communication_rating') ?: 0
            ]
        ];
            
        return view('reviews.index', compact('reviews', 'stats', 'prestataireId'));
    }
    
    /**
     * Store a newly created review in storage.
     */
    /**
     * Show the form for creating a new review.
     */
    public function create(Request $request): View
    {
        $prestataireId = $request->query('prestataire');
        $bookingId = $request->query('booking');

        return view('reviews.create', compact('prestataireId', 'bookingId'));
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'prestataire_id' => 'required|exists:prestataires,id',
        ]);

        Review::create([
            'client_id' => Auth::user()->id,
            'prestataire_id' => $request->prestataire_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Avis ajouté avec succès.');
    }

    /**
     * Display the specified review.
     */
    public function show(Review $review): View
    {
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(Review $review): View
    {
        $this->authorize('update', $review);
        
        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Avis modifié avec succès.');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);
        
        $review->delete();

        return redirect()->back()->with('success', 'Avis supprimé avec succès.');
    }
    
    /**
     * Display reviews with photos.
     */
    public function withPhotos(Request $request): View
    {
        // Default to null if no prestataire ID is provided
        $prestataireId = $request->query('prestataire_id');
        
        $reviews = Review::with(['client.user', 'prestataire.user'])
            ->whereRaw("JSON_LENGTH(IFNULL(photos, '[]')) > 0");
            
        // Filter by prestataire if ID is provided
        if ($prestataireId) {
            $reviews->where('prestataire_id', $prestataireId);
        }
        
        $reviews = $reviews->latest()->paginate(10);
        
        // Statistiques pour les avis
        $stats = [
            'total_reviews' => Review::count(),
            'average_rating' => Review::avg('rating') ?: 0,
            'reviews_with_photos' => Review::whereRaw("JSON_LENGTH(IFNULL(photos, '[]')) > 0")->count(),
            'detailed_averages' => [
                'punctuality' => Review::avg('punctuality_rating') ?: 0,
                'quality' => Review::avg('quality_rating') ?: 0,
                'value' => Review::avg('value_rating') ?: 0,
                'communication' => Review::avg('communication_rating') ?: 0
            ]
        ];
            
        return view('reviews.index', compact('reviews', 'stats', 'prestataireId'));
    }
    
    /**
     * Display reviews with satisfaction certificates.
     */
    public function certificates(Request $request): View
    {
        // Default to null if no prestataire ID is provided
        $prestataireId = $request->query('prestataire_id');
        
        // Assuming certificates are reviews with high ratings (4 or 5)
        $reviews = Review::with(['client.user', 'prestataire.user'])
            ->where('rating', '>=', 4);
            
        // Filter by prestataire if ID is provided
        if ($prestataireId) {
            $reviews->where('prestataire_id', $prestataireId);
        }
        
        $reviews = $reviews->latest()->paginate(10);
        
        // Statistiques pour les avis
        $stats = [
            'total_reviews' => Review::count(),
            'average_rating' => Review::avg('rating') ?: 0,
            'reviews_with_photos' => Review::whereRaw("JSON_LENGTH(IFNULL(photos, '[]')) > 0")->count(),
            'detailed_averages' => [
                'punctuality' => Review::avg('punctuality_rating') ?: 0,
                'quality' => Review::avg('quality_rating') ?: 0,
                'value' => Review::avg('value_rating') ?: 0,
                'communication' => Review::avg('communication_rating') ?: 0
            ]
        ];
            
        return view('reviews.index', compact('reviews', 'stats', 'prestataireId'));
    }
}
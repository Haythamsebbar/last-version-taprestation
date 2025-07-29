<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\ClientRequest;
use App\Models\Prestataire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    /**
     * Affiche la liste des offres.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Offer::with(['prestataire.user', 'clientRequest.client.user']);
        
        // Filtrage par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filtrage par prix
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
        
        // Filtrage par date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filtrage par prestataire
        if ($request->filled('prestataire')) {
            $query->whereHas('prestataire.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->prestataire . '%');
            });
        }
        
        // Recherche par message
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', '%' . $search . '%')
                  ->orWhere('details', 'like', '%' . $search . '%');
            });
        }
        
        $offers = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => Offer::count(),
            'pending' => Offer::where('status', 'pending')->count(),
            'accepted' => Offer::where('status', 'accepted')->count(),
            'rejected' => Offer::where('status', 'rejected')->count(),
            'avg_price' => round(Offer::avg('price') ?? 0, 2),
            'conversion_rate' => $this->calculateConversionRate(),
        ];
        
        return view('admin.offers.index-modern', [
            'offers' => $offers,
            'stats' => $stats,
        ]);
    }
    
    /**
     * Affiche les détails d'une offre.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $offer = Offer::with([
            'prestataire.user',
            'clientRequest.client.user',
            'clientRequest.category'
        ])->findOrFail($id);
        
        return view('admin.offers.show', [
            'offer' => $offer,
        ]);
    }
    
    /**
     * Met à jour le statut d'une offre.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        $offer = Offer::findOrFail($id);
        $offer->status = $request->status;
        $offer->admin_notes = $request->admin_notes;
        $offer->save();
        
        return redirect()->route('administrateur.offers.show', $offer->id)
            ->with('success', 'Le statut de l\'offre a été mis à jour avec succès.');
    }
    
    /**
     * Modère une offre (approuve ou rejette).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moderate(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'moderation_reason' => 'nullable|string|max:500',
        ]);
        
        $offer = Offer::findOrFail($id);
        
        if ($request->action === 'approve') {
            $offer->is_moderated = true;
            $offer->moderation_status = 'approved';
        } else {
            $offer->is_moderated = true;
            $offer->moderation_status = 'rejected';
            $offer->status = 'rejected';
        }
        
        $offer->moderation_reason = $request->moderation_reason;
        $offer->moderated_by = Auth::id();
        $offer->moderated_at = now();
        $offer->save();
        
        $message = $request->action === 'approve' 
            ? 'L\'offre a été approuvée avec succès.' 
            : 'L\'offre a été rejetée avec succès.';
        
        return redirect()->route('administrateur.offers.show', $offer->id)
            ->with('success', $message);
    }
    
    /**
     * Supprime une offre.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();
        
        return redirect()->route('administrateur.offers.index')
            ->with('success', 'L\'offre a été supprimée avec succès.');
    }
    
    /**
     * Export des offres.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $query = Offer::with(['prestataire.user', 'clientRequest']);
        
        // Appliquer les mêmes filtres que l'index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $offers = $query->get();
        
        $filename = 'offres_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($offers) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Prestataire',
                'Demande Client',
                'Prix',
                'Statut',
                'Délai (jours)',
                'Créé le',
                'Mis à jour le'
            ]);
            
            // Données
            foreach ($offers as $offer) {
                fputcsv($file, [
                    $offer->id,
                    $offer->prestataire->user->name ?? 'N/A',
                    $offer->clientRequest->title ?? 'N/A',
                    $offer->price ?? 'N/A',
                    $offer->status,
                    $offer->estimated_duration ?? 'N/A',
                    $offer->created_at,
                    $offer->updated_at
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Affiche les statistiques des offres.
     *
     * @return \Illuminate\View\View
     */
    public function analytics()
    {
        // Statistiques par mois (6 derniers mois)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'offers' => Offer::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'accepted' => Offer::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'accepted')
                    ->count(),
            ];
        }
        
        // Top prestataires par nombre d'offres
        $topPrestataires = Prestataire::withCount('offers')
            ->with('user')
            ->orderBy('offers_count', 'desc')
            ->take(10)
            ->get();
        
        // Statistiques par prix
        $priceRanges = [
            '0-100' => Offer::whereBetween('price', [0, 100])->count(),
            '101-500' => Offer::whereBetween('price', [101, 500])->count(),
            '501-1000' => Offer::whereBetween('price', [501, 1000])->count(),
            '1001-5000' => Offer::whereBetween('price', [1001, 5000])->count(),
            '5000+' => Offer::where('price', '>', 5000)->count(),
        ];
        
        // Temps de réponse moyen
        $avgResponseTime = $this->calculateAverageResponseTime();
        
        return view('admin.offers.analytics', [
            'monthlyStats' => $monthlyStats,
            'topPrestataires' => $topPrestataires,
            'priceRanges' => $priceRanges,
            'avgResponseTime' => $avgResponseTime,
        ]);
    }
    
    /**
     * Calcule le taux de conversion des offres.
     *
     * @return float
     */
    private function calculateConversionRate()
    {
        $totalOffers = Offer::count();
        $acceptedOffers = Offer::where('status', 'accepted')->count();
        
        return $totalOffers > 0 ? round(($acceptedOffers / $totalOffers) * 100, 1) : 0;
    }
    
    /**
     * Calcule le temps de réponse moyen en heures.
     *
     * @return float
     */
    private function calculateAverageResponseTime()
    {
        $offers = Offer::with('clientRequest')
            ->whereNotNull('created_at')
            ->get();
        
        if ($offers->isEmpty()) {
            return 0;
        }
        
        $totalHours = 0;
        $count = 0;
        
        foreach ($offers as $offer) {
            if ($offer->clientRequest && $offer->clientRequest->created_at) {
                $hours = $offer->clientRequest->created_at->diffInHours($offer->created_at);
                $totalHours += $hours;
                $count++;
            }
        }
        
        return $count > 0 ? round($totalHours / $count, 1) : 0;
    }
}
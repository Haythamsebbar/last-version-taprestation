<?php

namespace App\Http\Controllers;

use App\Models\UrgentSale;
use App\Models\UrgentSaleContact;
use App\Models\UrgentSaleReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UrgentSaleController extends Controller
{
    /**
     * Afficher la liste des ventes urgentes publiques
     */
    public function index(Request $request)
    {
        $query = UrgentSale::active()->with(['prestataire.user']);
        
        // Recherche par mot-clé
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        // Filtrage par localisation
        if ($request->filled('city')) {
            $query->whereHas('prestataire', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->city . '%');
            });
        }
        
        // Filtrage par prix
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
        
        // Filtrage par condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        
        // Filtres spéciaux
        if ($request->filled('urgent_only')) {
            $query->where('is_urgent', true);
        }
        
        // Tri
        $sortBy = $request->get('sort', 'created_at');
        
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'urgent':
                $query->orderBy('is_urgent', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            case 'recent':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_urgent', 'desc')
                      ->orderBy('created_at', 'desc');
        }
        
        $urgentSales = $query->paginate(12)->withQueryString();
        
        // Données pour les filtres
        $priceRange = UrgentSale::active()->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        $conditions = UrgentSale::CONDITION_OPTIONS;
        
        // Ventes urgentes en vedette
        $featuredSales = UrgentSale::active()->urgent()
                                  ->with(['prestataire.user'])
                                  ->limit(6)
                                  ->get();
        
        return view('urgent-sales.index', compact('urgentSales', 'priceRange', 'conditions', 'featuredSales'));
    }
    
    /**
     * Afficher les détails d'une vente urgente
     */
    public function show(UrgentSale $urgentSale)
    {
        // Vérifier que la vente est active
        if (!$urgentSale->isActive()) {
            abort(404);
        }
        
        // Incrémenter le compteur de vues
        $urgentSale->increment('views_count');
        
        $urgentSale->load(['prestataire.user']);
        
        // Autres ventes du même prestataire
        $otherSales = $urgentSale->prestataire->urgentSales()
                                 ->active()
                                 ->where('id', '!=', $urgentSale->id)
                                 ->limit(3)
                                 ->get();
        
        // Ventes similaires (même gamme de prix)
        $priceMin = $urgentSale->price * 0.7;
        $priceMax = $urgentSale->price * 1.3;
        
        $similarSales = UrgentSale::active()
                                 ->where('id', '!=', $urgentSale->id)
                                 ->whereBetween('price', [$priceMin, $priceMax])
                                 ->with(['prestataire.user'])
                                 ->limit(4)
                                 ->get();
        
        return view('urgent-sales.show', compact('urgentSale', 'otherSales', 'similarSales'));
    }
    
    /**
     * Contacter le vendeur
     */
    public function contact(Request $request, UrgentSale $urgentSale)
    {
        if (!$urgentSale->canBeContacted()) {
            return back()->with('error', 'Ce produit n\'est plus disponible.');
        }
        
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Créer le contact
        UrgentSaleContact::create([
            'urgent_sale_id' => $urgentSale->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'phone' => $request->phone,
            'email' => $request->email ?? Auth::user()->email,
            'status' => 'pending'
        ]);
        
        // Créer un message dans la messagerie
        \App\Models\Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $urgentSale->prestataire->user_id,
            'content' => "Concernant votre vente urgente '{$urgentSale->title}': " . $request->message,
            'status' => 'approved'
        ]);
        
        // Incrémenter le compteur de contacts
        $urgentSale->incrementContacts();
        
        return back()->with('success', 'Votre message est envoyé');
    }
    
    /**
     * Signaler une vente urgente
     */
    public function report(Request $request, UrgentSale $urgentSale)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|in:inappropriate,spam,fake,other',
            'details' => 'nullable|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Vérifier si l'utilisateur a déjà signalé cette vente
        $existingReport = UrgentSaleReport::where('urgent_sale_id', $urgentSale->id)
                                         ->where('user_id', Auth::id())
                                         ->first();
        
        if ($existingReport) {
            return back()->with('error', 'Vous avez déjà signalé cette vente.');
        }
        
        UrgentSaleReport::create([
            'urgent_sale_id' => $urgentSale->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'description' => $request->details,
            'status' => 'pending'
        ]);
        
        return back()->with('success', 'Votre signalement a été envoyé. Merci de nous aider à maintenir la qualité de la plateforme.');
    }
}
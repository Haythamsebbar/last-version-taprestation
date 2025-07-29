<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\UrgentSale;
use App\Models\UrgentSaleContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UrgentSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $prestataire = Auth::user()->prestataire;
        
        $query = UrgentSale::where('prestataire_id', $prestataire->id)
            ->with(['contacts' => function($q) {
                $q->pending()->recent();
            }]);
        
        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $urgentSales = $query->latest()->paginate(12);
        
        // Statistiques
        $stats = [
            'total' => UrgentSale::where('prestataire_id', $prestataire->id)->count(),
            'active' => UrgentSale::where('prestataire_id', $prestataire->id)->where('status', 'active')->count(),
            'sold' => UrgentSale::where('prestataire_id', $prestataire->id)->where('status', 'sold')->count(),
            'total_views' => UrgentSale::where('prestataire_id', $prestataire->id)->sum('views_count'),
            'total_contacts' => UrgentSale::where('prestataire_id', $prestataire->id)->sum('contact_count'),
            'pending_contacts' => UrgentSaleContact::whereHas('urgentSale', function($q) use ($prestataire) {
                $q->where('prestataire_id', $prestataire->id);
            })->pending()->count()
        ];
        
        return view('prestataire.urgent-sales.index', compact('urgentSales', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('prestataire.urgent-sales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,good,used,fair',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_urgent' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $prestataire = Auth::user()->prestataire;
        
        $urgentSale = new UrgentSale();
        $urgentSale->prestataire_id = $prestataire->id;
        $urgentSale->title = $request->title;
        $urgentSale->description = $request->description;
        $urgentSale->price = $request->price;
        $urgentSale->condition = $request->condition;
        $urgentSale->quantity = $request->quantity ?? 1;
        $urgentSale->location = $request->location;
        $urgentSale->latitude = $request->latitude;
        $urgentSale->longitude = $request->longitude;
        $urgentSale->is_urgent = $request->boolean('is_urgent');
        
        // Gestion des photos
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('urgent-sales', 'public');
                $photos[] = $path;
            }
        }
        $urgentSale->photos = $photos;
        
        $urgentSale->save();
        
        return redirect()->route('prestataire.urgent-sales.index')
            ->with('success', 'Produit ajouté avec succès à la vente urgente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(UrgentSale $urgentSale)
    {
        $this->authorize('view', $urgentSale);
        
        $contacts = $urgentSale->contacts()->with('user')->recent()->paginate(10);
        
        return view('prestataire.urgent-sales.show', compact('urgentSale', 'contacts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UrgentSale $urgentSale)
    {
        $this->authorize('update', $urgentSale);
        
        if (!$urgentSale->canBeEdited()) {
            return redirect()->route('prestataire.urgent-sales.index')
                ->with('error', 'Ce produit ne peut plus être modifié.');
        }
        
        return view('prestataire.urgent-sales.edit', compact('urgentSale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UrgentSale $urgentSale)
    {
        $this->authorize('update', $urgentSale);
        
        if (!$urgentSale->canBeEdited()) {
            return redirect()->route('prestataire.urgent-sales.index')
                ->with('error', 'Ce produit ne peut plus être modifié.');
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,good,used,fair',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_urgent' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $urgentSale->title = $request->title;
        $urgentSale->description = $request->description;
        $urgentSale->price = $request->price;
        $urgentSale->condition = $request->condition;
        $urgentSale->quantity = $request->quantity ?? 1;
        $urgentSale->location = $request->location;
        $urgentSale->latitude = $request->latitude;
        $urgentSale->longitude = $request->longitude;
        $urgentSale->is_urgent = $request->boolean('is_urgent');
        
        // Gestion des photos
        if ($request->hasFile('photos')) {
            // Supprimer les anciennes photos
            if ($urgentSale->photos) {
                foreach ($urgentSale->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }
            
            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('urgent-sales', 'public');
                $photos[] = $path;
            }
            $urgentSale->photos = $photos;
        }
        
        $urgentSale->save();
        
        return redirect()->route('prestataire.urgent-sales.index')
            ->with('success', 'Produit mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UrgentSale $urgentSale)
    {
        $this->authorize('delete', $urgentSale);
        
        // Supprimer les photos
        if ($urgentSale->photos) {
            foreach ($urgentSale->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }
        
        $urgentSale->delete();
        
        return redirect()->route('prestataire.urgent-sales.index')
            ->with('success', 'Produit supprimé avec succès!');
    }
    
    /**
     * Changer le statut d'une vente
     */
    public function updateStatus(Request $request, UrgentSale $urgentSale)
    {
        $this->authorize('update', $urgentSale);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,sold,withdrawn'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        
        $urgentSale->status = $request->status;
        $urgentSale->save();
        
        $message = match($request->status) {
            'sold' => 'Produit marqué comme vendu!',
            'withdrawn' => 'Produit retiré de la vente!',
            'active' => 'Produit remis en vente!',
            default => 'Statut mis à jour!'
        };
        
        return back()->with('success', $message);
    }
    
    /**
     * Afficher les contacts pour une vente
     */
    public function contacts(UrgentSale $urgentSale)
    {
        $this->authorize('view', $urgentSale);
        
        $contacts = $urgentSale->contacts()->with('user')->recent()->paginate(15);
        
        return view('prestataire.urgent-sales.contacts', compact('urgentSale', 'contacts'));
    }
    
    /**
     * Répondre à un contact
     */
    public function respondToContact(Request $request, UrgentSaleContact $contact)
    {
        $this->authorize('update', $contact->urgentSale);
        
        $validator = Validator::make($request->all(), [
            'response' => 'required|string|max:1000'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        
        $contact->markAsResponded($request->response);
        
        return back()->with('success', 'Réponse envoyée avec succès!');
    }
}

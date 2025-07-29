<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\EquipmentRentalRequest;
use App\Models\EquipmentRental;
use App\Models\EquipmentReview;
use App\Http\Requests\Prestataire\StoreEquipmentRequest;
use App\Services\Prestataire\EquipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    /**
     * Affiche la liste des équipements du prestataire
     */
        public function index(Request $request)
    {
        $prestataire = Auth::user()->prestataire;
        
        
        $query = $prestataire->equipment()
                            ->with(['categories', 'rentalRequests', 'rentals'])
                            ->withCount(['rentalRequests', 'rentals', 'reviews']);
        
        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('equipment_categories.id', $request->category);
            });
        }
        
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Tri
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'price':
                $query->orderBy('price_per_day', $sortOrder);
                break;
            case 'rating':
                $query->orderBy('average_rating', $sortOrder);
                break;
            case 'rentals':
                $query->orderBy('total_rentals', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }
        
        $equipment = $query->paginate(12);
        $categories = EquipmentCategory::active()->main()->with('children')->get();

        $rentalRequests = $prestataire->equipmentRentalRequests()
            ->with(['equipment', 'client.user'])
            ->latest()
            ->take(10)
            ->get();

        // Statistiques
        $stats = [
            'total' => $prestataire->equipment()->count(),
            'active' => $prestataire->equipment()->active()->count(),
            'rented' => $prestataire->equipment()->where('status', 'rented')->count(),
            'pending_requests' => $prestataire->equipmentRentalRequests()->pending()->count(),
        ];

        return view('prestataire.equipment.index', compact('equipment', 'categories', 'stats', 'rentalRequests'));
    }
    
    /**
     * Affiche le formulaire de création d'équipement
     */
    public function create()
    {
        $categories = EquipmentCategory::with('children')->get();
        return view('prestataire.equipment.create', compact('categories'));
    }

    /**
     * Enregistre un nouvel équipement
     */
    public function store(StoreEquipmentRequest $request, EquipmentService $equipmentService)
    {
        $prestataire = Auth::user()->prestataire;
        if (!$prestataire) {
            return redirect()->back()->with('error', 'Vous devez être un prestataire pour ajouter un équipement.');
        }

        $equipment = $equipmentService->createEquipment($request->validated());

        return redirect()->route('prestataire.equipment.show', $equipment)
                        ->with('success', 'Équipement ajouté avec succès!');
    }
    
    /**
     * Affiche les détails d'un équipement
     */
    public function show(Equipment $equipment)
    {
        // $this->authorize('view', $equipment);
        
        $equipment->load([
            'categories',
            'rentalRequests' => function ($query) {
                $query->latest()->with('client.user');
            },
            'rentals' => function ($query) {
                $query->latest()->with('client.user');
            },
            'reviews' => function ($query) {
                $query->approved()->latest()->with('client.user');
            }
        ]);
        
        // Statistiques
        $stats = [
            'total_requests' => $equipment->rentalRequests()->count(),
            'pending_requests' => $equipment->rentalRequests()->pending()->count(),
            'total_rentals' => $equipment->rentals()->count(),
            'active_rentals' => $equipment->rentals()->active()->count(),
            'total_revenue' => $equipment->rentals()->sum('final_amount'),
            'average_rating' => $equipment->reviews()->approved()->avg('overall_rating'),
            'total_reviews' => $equipment->reviews()->approved()->count(),
        ];
        
        return view('prestataire.equipment.show', compact('equipment', 'stats'));
    }
    
    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Equipment $equipment)
    {
        // $this->authorize('update', $equipment);
        
        $categories = EquipmentCategory::active()->main()->with('children')->get();
        $selectedCategories = $equipment->categories->pluck('id')->toArray();
        
        return view('prestataire.equipment.edit', compact('equipment', 'categories', 'selectedCategories'));
    }
    
    /**
     * Met à jour un équipement
     */
    public function update(Request $request, Equipment $equipment)
    {
        // $this->authorize('update', $equipment);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'technical_specifications' => 'nullable|string',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:equipment_categories,id',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,webp|max:5120',
            'main_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            
            // Détails techniques
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'power_requirements' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            
            // Tarification
            'price_per_hour' => 'nullable|numeric|min:0',
            'daily_rate' => 'required|numeric|min:1',
            'price_per_week' => 'nullable|numeric|min:0',
            'price_per_month' => 'nullable|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'delivery_cost' => 'nullable|numeric|min:0',
            'delivery_available' => 'boolean',
            
            // État et disponibilité
            'condition' => 'required|in:new,excellent,very_good,good,fair,poor',
            'status' => 'nullable|in:active,inactive,maintenance,rented',
            'is_available' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
            
            // Localisation
            'address' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'required|string|max:100',
            'delivery_radius' => 'nullable|integer|min:0|max:200',
            
            // Conditions de location
            'minimum_rental_days' => 'nullable|integer|min:1',
            'maximum_rental_days' => 'nullable|integer|min:1',
            'age_restriction' => 'nullable|integer|min:16|max:99',
            'experience_required' => 'boolean',
            'insurance_required' => 'boolean',
            'license_required' => 'boolean',
            'rental_conditions' => 'nullable|string',
            
            // Instructions et accessoires
            'usage_instructions' => 'nullable|string',
            'safety_instructions' => 'nullable|string',
            'accessories' => 'nullable|string',
        ]);
        
        // Mise à jour du slug si le nom a changé
        if ($equipment->name !== $validated['name']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $equipment->id);
        }
        
        // Gestion de la photo principale
        if ($request->hasFile('main_photo')) {
            // Supprimer l'ancienne photo
            if ($equipment->main_photo) {
                Storage::disk('public')->delete($equipment->main_photo);
            }
            $validated['main_photo'] = $request->file('main_photo')
                ->store('equipment_photos', 'public');
        }
        
        // Gestion des photos de galerie
        if ($request->hasFile('photos')) {
            // Supprimer les anciennes photos
            if ($equipment->photos) {
                foreach ($equipment->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }
            
            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $photos[] = $photo->store('equipment_photos', 'public');
            }
            $validated['photos'] = $photos;
        }
        
        // Les champs sont déjà des strings, pas de conversion nécessaire pour les accessoires
        $equipment->update($validated);
        
        // Mise à jour des catégories
        $equipment->categories()->sync($validated['categories']);
        
        return redirect()->route('prestataire.equipment.show', $equipment)
                        ->with('success', 'Équipement mis à jour avec succès!');
    }
    
    /**
     * Supprime un équipement
     */
    public function destroy(Equipment $equipment)
    {
        $this->authorize('delete', $equipment);
        
        // Vérifier qu'il n'y a pas de locations actives
        if ($equipment->rentals()->active()->exists()) {
            return back()->with('error', 'Impossible de supprimer un équipement avec des locations actives.');
        }
        
        // Supprimer les photos
        if ($equipment->main_photo) {
            Storage::disk('public')->delete($equipment->main_photo);
        }
        
        if ($equipment->photos) {
            foreach ($equipment->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }
        
        $equipment->delete();
        
        return redirect()->route('prestataire.equipment.index')
                        ->with('success', 'Équipement supprimé avec succès!');
    }
    
    /**
     * Active/désactive un équipement
     */
    public function toggleStatus(Equipment $equipment)
    {
        $this->authorize('update', $equipment);
        
        $newStatus = $equipment->status === 'active' ? 'inactive' : 'active';
        $equipment->update(['status' => $newStatus]);
        
        $message = $newStatus === 'active' ? 'Équipement activé' : 'Équipement désactivé';
        
        return back()->with('success', $message);
    }
    
    /**
     * Duplique un équipement
     */
    public function duplicate(Equipment $equipment)
    {
        $this->authorize('view', $equipment);
        
        $newEquipment = $equipment->replicate();
        $newEquipment->name = $equipment->name . ' (Copie)';
        $newEquipment->slug = $this->generateUniqueSlug($newEquipment->name);
        $newEquipment->status = 'inactive';
        $newEquipment->total_rentals = 0;
        $newEquipment->total_reviews = 0;
        $newEquipment->average_rating = 0;
        $newEquipment->view_count = 0;
        $newEquipment->last_rented_at = null;
        $newEquipment->save();
        
        // Copier les catégories
        $newEquipment->categories()->attach($equipment->categories->pluck('id'));
        
        return redirect()->route('prestataire.equipment.edit', $newEquipment)
                        ->with('success', 'Équipement dupliqué avec succès!');
    }
    
    /**
     * Génère un slug unique
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $query = Equipment::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
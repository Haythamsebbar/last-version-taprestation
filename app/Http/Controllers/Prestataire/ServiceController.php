<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Category;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreServiceRequest;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Affiche la liste des services du prestataire.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;

        // Base query
        $query = $prestataire->services()->with('categories', 'bookings');

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status == 'reservable') {
                $query->where('reservable', true);
            } elseif ($request->status == 'non-reservable') {
                $query->where('reservable', false);
            }
        }

        // Sorting
        $sort = $request->input('sort', 'created_at_desc');
        switch ($sort) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $services = $query->paginate(12);

        // Stats
        $stats = [
            'total' => $prestataire->services()->count(),
            'reservable' => $prestataire->services()->where('reservable', true)->count(),
            'total_bookings' => Booking::whereIn('service_id', $prestataire->services->pluck('id'))->count(),
            'confirmed_bookings' => Booking::whereIn('service_id', $prestataire->services->pluck('id'))->where('status', 'confirmed')->count(),
        ];

        $categories = Category::all();

        return view('prestataire.services.index', [
            'services' => $services,
            'prestataire' => $prestataire,
            'stats' => $stats,
            'categories' => $categories,
        ]);
    }

    /**
     * Affiche le formulaire de création d'un service.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        
        return view('prestataire.services.create', [
            'categories' => $categories
        ]);
    }

    /**
     * Enregistre un nouveau service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreServiceRequest $request)
    {
        $prestataire = Auth::user()->prestataire;

        $data = $request->validated();
        $data['reservable'] = $request->has('reservable');

        $service = $prestataire->services()->create($data);

        if ($request->has('categories')) {
            $service->categories()->sync($request->validated()['categories']);
        }

        if ($request->hasFile('images')) {
            $this->handleImageUpload($request->file('images'), $service);
        }

        return redirect()->route('prestataire.services.index')
            ->with('success', 'Service créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un service.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\View\View
     */
    public function edit(Service $service)
    {
        // Vérifier que le service appartient bien au prestataire connecté
        $this->authorize('update', $service);
        
        $categories = Category::all();
        $selectedCategories = $service->categories->pluck('id')->toArray();

        return view('prestataire.services.edit', [
            'service' => $service->load('images'), // Eager load images
            'categories' => $categories,
            'selectedCategories' => $selectedCategories
        ]);
    }

    /**
     * Met à jour un service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);

        $data = $request->validated();
        $data['reservable'] = $request->has('reservable');

        $service->update($data);

        if ($request->has('categories')) {
            $service->categories()->sync($request->validated()['categories']);
        } else {
            $service->categories()->sync([]);
        }

        if ($request->hasFile('images')) {
            $this->handleImageUpload($request->file('images'), $service);
        }

        return redirect()->route('prestataire.services.index')
            ->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * Supprime un service.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Service $service)
    {
        // Vérifier que le service appartient bien au prestataire connecté
        $this->authorize('delete', $service);
        
        // Supprimer les images associées
        foreach ($service->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }
        
        $service->categories()->detach();
        $service->delete();
        
        return redirect()->route('prestataire.services.index')
            ->with('success', 'Service supprimé avec succès.');
    }
    
    /**
     * Gère l'upload des images pour un service.
     *
     * @param  array  $images
     * @param  \App\Models\Service  $service
     * @return void
     */
    private function handleImageUpload(array $images, Service $service)
    {
        $lastOrder = $service->images()->max('order') ?? 0;

        foreach ($images as $index => $image) {
            $originalName = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $fileName = time() . '_' . $index . '_' . uniqid() . '.' . $extension;
            
            $path = $image->storeAs('services', $fileName, 'public');
            
            ServiceImage::create([
                'service_id' => $service->id,
                'image_path' => $path,
                'original_name' => $originalName,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'order' => ++$lastOrder,
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prestataire;
use App\Models\Category;
use App\Models\Service;
use App\Models\Booking;
use App\Models\ClientRequest;
use App\Models\Message;
use App\Models\Review;
use App\Models\Offer;
use App\Models\EquipmentRentalRequest;
use App\Models\UrgentSaleContact;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du client.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $client = $user->client;

        // Welcome message
        $welcomeMessage = 'Bonjour, ' . $user->name . '!';

        // Shortcuts
        $shortcuts = [
            [
                'name' => 'Rechercher un prestataire',
                'description' => 'Trouvez les meilleurs talents.',
                'icon' => 'fas fa-search',
                'url' => route('client.prestataires.index')
            ],
            [
                'name' => 'Voir mes réservations',
                'description' => 'Gérez vos rendez-vous.',
                'icon' => 'fas fa-calendar-alt',
                'url' => route('client.bookings.index')
            ],
            [
                'name' => 'Mes demandes de location',
                'description' => 'Gérez vos demandes de matériel.',
                'icon' => 'fas fa-tools',
                'url' => route('client.equipment-rental-requests.index')
            ],
            [
                'name' => 'Messagerie',
                'description' => 'Discutez avec les prestataires.',
                'icon' => 'fas fa-comments',
                'url' => url('/messaging')
            ]
        ];

        // Recent bookings
        $recentBookings = Booking::where('client_id', $client->id)
            ->with(['prestataire.user', 'service'])
            ->orderBy('start_datetime', 'desc')
            ->take(3)
            ->get();

        // Unread messages
        $unreadMessages = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $recentRentalRequests = EquipmentRentalRequest::where('client_id', $client->id)
            ->with(['equipment.prestataire.user'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Recent urgent sale contacts
        $recentUrgentSaleContacts = UrgentSaleContact::where('user_id', $user->id)
            ->with(['urgentSale.prestataire.user'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Unified recent requests (all types combined)
        $allRequests = collect();
        
        // Add bookings
        foreach($recentBookings as $booking) {
            $allRequests->push([
                'type' => 'service',
                'title' => $booking->service->title,
                'prestataire' => $booking->prestataire->user->name,
                'date' => $booking->start_datetime,
                'status' => $booking->status,
                'created_at' => $booking->created_at,
                'badge_color' => 'bg-blue-100 text-blue-800',
                'badge_text' => 'Service'
            ]);
        }
        
        // Add equipment rentals
        foreach($recentRentalRequests as $rental) {
            $allRequests->push([
                'type' => 'equipment',
                'title' => $rental->equipment->name,
                'prestataire' => $rental->equipment->prestataire->user->name,
                'date' => $rental->created_at,
                'status' => $rental->status,
                'created_at' => $rental->created_at,
                'badge_color' => 'bg-green-100 text-green-800',
                'badge_text' => 'Matériel'
            ]);
        }
        
        // Add urgent sale contacts
        foreach($recentUrgentSaleContacts as $contact) {
            $allRequests->push([
                'type' => 'urgent_sale',
                'title' => $contact->urgentSale->title,
                'prestataire' => $contact->urgentSale->prestataire->user->name,
                'date' => $contact->created_at,
                'status' => $contact->status,
                'created_at' => $contact->created_at,
                'badge_color' => 'bg-red-100 text-red-800',
                'badge_text' => 'Vente urgente'
            ]);
        }
        
        // Sort all requests by date (most recent first)
        $unifiedRequests = $allRequests->sortByDesc('created_at')->take(7);

        return view('client.dashboard', [
            'client' => $client,
            'welcomeMessage' => $welcomeMessage,
            'shortcuts' => $shortcuts,
            'recentBookings' => $recentBookings,
            'recentRentalRequests' => $recentRentalRequests,
            'recentUrgentSaleContacts' => $recentUrgentSaleContacts,
            'unifiedRequests' => $unifiedRequests,
            'unreadMessages' => $unreadMessages,
        ]);
    }

    /**
     * Display all unified requests for the client
     */
    public function allRequests()
    {
        $client = auth()->user()->client;
        
        // Get all bookings
        $allBookings = $client->bookings()->with(['service', 'prestataire.user'])->get();
        
        // Get all equipment rental requests
        $allRentalRequests = $client->equipmentRentalRequests()->with(['equipment.prestataire.user'])->get();
        
        // Get all urgent sale contacts
        $allUrgentSaleContacts = $client->urgentSaleContacts()->with(['urgentSale.prestataire.user'])->get();
        
        // Create unified collection with all requests
        $allUnifiedRequests = collect();
        
        // Add bookings
        foreach ($allBookings as $booking) {
            $allUnifiedRequests->push([
                'id' => $booking->id,
                'type' => 'service',
                'title' => $booking->service->title,
                'prestataire' => 'Avec ' . $booking->prestataire->user->name,
                'date' => $booking->start_datetime,
                'status' => $booking->status,
                'badge_text' => 'Service',
                'badge_color' => 'bg-blue-100 text-blue-800',
                'created_at' => $booking->created_at,
            ]);
        }
        
        // Add equipment rental requests
        foreach ($allRentalRequests as $request) {
            $allUnifiedRequests->push([
                'id' => $request->id,
                'type' => 'equipment',
                'title' => $request->equipment->name,
                'prestataire' => 'Auprès de ' . $request->equipment->prestataire->user->name,
                'date' => $request->created_at,
                'status' => $request->status,
                'badge_text' => 'Matériel',
                'badge_color' => 'bg-green-100 text-green-800',
                'created_at' => $request->created_at,
            ]);
        }
        
        // Add urgent sale contacts
        foreach ($allUrgentSaleContacts as $contact) {
            $allUnifiedRequests->push([
                'id' => $contact->id,
                'type' => 'urgent_sale',
                'title' => $contact->urgentSale->title,
                'prestataire' => 'Contact avec ' . $contact->urgentSale->prestataire->user->name,
                'date' => $contact->created_at,
                'status' => $contact->status,
                'badge_text' => 'Vente urgente',
                'badge_color' => 'bg-red-100 text-red-800',
                'created_at' => $contact->created_at,
            ]);
        }
        
        // Sort by creation date (most recent first)
        $allUnifiedRequests = $allUnifiedRequests->sortByDesc('created_at');
        
        // Group by type for organized display
        $groupedRequests = [
            'service' => $allUnifiedRequests->where('type', 'service'),
            'equipment' => $allUnifiedRequests->where('type', 'equipment'),
            'urgent_sale' => $allUnifiedRequests->where('type', 'urgent_sale'),
        ];
        
        return view('client.requests.all', compact('allUnifiedRequests', 'groupedRequests'));
    }
    
    /**
     * Affiche la liste des prestataires approuvés.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function prestataires(Request $request)
    {
        $query = Prestataire::where('is_approved', true);
        
        // Filtrage par secteur d'activité
        if ($request->has('sector') && $request->sector) {
            $query->where('sector', 'like', '%' . $request->sector . '%');
        }
        
        // Filtrage par compétence
        if ($request->has('skill') && $request->skill) {
            $query->whereHas('skills', function($q) use ($request) {
                $q->where('skills.id', $request->skill);
            });
        }
        
        // Filtrage par catégorie de service
        if ($request->has('category') && $request->category) {
            $query->whereHas('services.categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }
        
        $prestataires = $query->with(['user', 'skills', 'services'])->paginate(12);
        $categories = Category::all();
        $sectors = Prestataire::where('is_approved', true)
            ->select('sector')
            ->distinct()
            ->pluck('sector');
        $skills = \App\Models\Skill::all();
        
        return view('client.prestataires.index', [
            'prestataires' => $prestataires,
            'categories' => $categories,
            'sectors' => $sectors,
            'skills' => $skills,
            'filters' => $request->only(['sector', 'skill', 'category'])
        ]);
    }
    
    /**
     * Affiche le profil du client.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        $client = $user->client;
        
        return view('client.profile', [
            'user' => $user,
            'client' => $client,
        ]);
    }
    
    /**
     * Met à jour le profil du client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();
        
        if ($client) {
            $client->location = $validated['location'] ?? $client->location;
            
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('profile_photos/clients', 'public');
                $client->photo = $photoPath;
            }
            
            $client->save();
        }
        
        return redirect()->route('client.profile')->with('success', 'Profil mis à jour avec succès.');
    }
}
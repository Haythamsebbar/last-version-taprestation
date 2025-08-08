<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\EquipmentRental;
use App\Models\Prestataire;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\UrgentSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of bookings for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'client') {
            $bookings = Booking::where('client_id', $user->client->id)
                ->with(['prestataire.user', 'service', 'timeSlot'])
                ->orderBy('start_datetime', 'desc')
                ->paginate(10);
        } elseif ($user->role === 'prestataire') {
            $bookings = Booking::where('prestataire_id', $user->prestataire->id)
                ->with(['client.user', 'service', 'timeSlot'])
                ->orderBy('start_datetime', 'desc')
                ->paginate(10);
        } else {
            abort(403, 'Unauthorized');
        }

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking
     */
    public function create(Service $service)
    {
        $prestataire = $service->prestataire;
        
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(30);
        $availableSlots = generate_time_slots($prestataire, $startDate, $endDate);

        return view('bookings.create', compact('service', 'prestataire', 'availableSlots'));
    }

    /**
     * Store a newly created booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'prestataire_id' => 'required|exists:prestataires,id',
            'start_datetime' => 'required|date',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        if ($user->role !== 'client') {
            abort(403, 'Seuls les clients peuvent créer des réservations.');
        }

        $service = Service::findOrFail($request->service_id);
        $prestataire = Prestataire::findOrFail($request->prestataire_id);
        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = $start_datetime->copy()->addMinutes($service->duration_minutes);

        // Basic check for availability (can be improved)
        $isBooked = Booking::where('prestataire_id', $prestataire->id)
            ->where(function ($query) use ($start_datetime, $end_datetime) {
                $query->where('start_datetime', '<', $end_datetime)
                      ->where('end_datetime', '>', $start_datetime);
            })->exists();

        if ($isBooked) {
            return redirect()->back()->with('error', 'Ce créneau n\'est plus disponible.');
        }

        // Create booking
        $booking = Booking::create([
            'client_id' => $user->client->id,
            'prestataire_id' => $request->prestataire_id,
            'service_id' => $request->service_id,
            'start_datetime' => $start_datetime,
            'end_datetime' => $end_datetime,
            'status' => 'pending',
            'total_price' => $service->price,
            'client_notes' => $request->client_notes,
        ]);

        // Load necessary relationships for notification
        $booking->load(['client.user', 'prestataire.user', 'service']);

        // Notify the prestataire
        $booking->prestataire->user->notify(new \App\Notifications\NewBookingNotification($booking));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Votre réservation a été créée avec succès!');
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();
        
        // Check if user can view this booking
        if ($user->role === 'client' && $booking->client_id !== $user->client->id) {
            abort(403);
        }
        if ($user->role === 'prestataire' && $booking->prestataire_id !== $user->prestataire->id) {
            abort(403);
        }

        $booking->load(['client.user', 'prestataire.user', 'service', 'timeSlot']);
        
        return view('bookings.show', compact('booking'));
    }

    /**
     * Confirm a booking (prestataire only)
     */
    public function confirm(Booking $booking)
    {
        $user = Auth::user();
        
        if ($user->role !== 'prestataire' || $booking->prestataire_id !== $user->prestataire->id) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Cette réservation ne peut pas être confirmée.');
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        $booking->client->user->notify(new \App\Notifications\BookingConfirmedNotification($booking));

        return redirect()->back()->with('success', 'Réservation confirmée avec succès!');
    }

    /**
     * Refuse a booking (prestataire only)
     */
    public function refuse(Request $request, Booking $booking)
    {
        $user = Auth::user();
        
        if ($user->role !== 'prestataire' || $booking->prestataire_id !== $user->prestataire->id) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Cette réservation ne peut pas être refusée.');
        }

        $request->validate([
            'refusal_reason' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status' => 'refused',
            'cancellation_reason' => $request->refusal_reason,
            'cancelled_at' => now(),
        ]);

        $booking->client->user->notify(new \App\Notifications\BookingRefusedNotification($booking));

        return redirect()->back()->with('success', 'Réservation refusée.');
    }

    /**
     * Cancel a booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'client' && $booking->client_id !== $user->client->id) {
            abort(403);
        }
        if ($user->role === 'prestataire' && $booking->prestataire_id !== $user->prestataire->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'Cette réservation ne peut pas être annulée.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
        ]);

        // Release the time slot
        if ($booking->timeSlot) {
            $booking->timeSlot->releaseLock();
        }

        // Send notification to the other party
        if ($user->role === 'client') {
            // Send notification to prestataire
            $booking->prestataire->user->notify(new \App\Notifications\BookingCancelledNotification($booking));
        } else {
            // Send notification to client
            $booking->client->user->notify(new \App\Notifications\BookingCancelledNotification($booking));
        }

        return redirect()->back()->with('success', 'Réservation annulée avec succès.');
    }

    /**
     * Mark booking as completed (prestataire only)
     */
    public function complete(Booking $booking)
    {
        $user = Auth::user();
        
        if ($user->role !== 'prestataire' || $booking->prestataire_id !== $user->prestataire->id) {
            abort(403);
        }

        if ($booking->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Seules les réservations confirmées peuvent être marquées comme terminées.');
        }

        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        

        return redirect()->back()->with('success', 'Réservation marquée comme terminée!');
    }

    /**
     * Display bookings for clients with filtering options
     */
    public function clientBookings(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'client') {
            abort(403, 'Accès non autorisé.');
        }

        $query = Booking::where('client_id', $user->client->id);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'past':
                    $query->past();
                    break;
                case 'last_month':
                    $query->where('start_datetime', '>=', now()->subMonth())
                          ->where('start_datetime', '<=', now());
                    break;
                case 'last_3_months':
                    $query->where('start_datetime', '>=', now()->subMonths(3))
                          ->where('start_datetime', '<=', now());
                    break;
            }
        }
        
        $bookings = $query->with(['prestataire.user', 'service', 'timeSlot'])
            ->orderBy('start_datetime', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('client.bookings.index', compact('bookings'));
    }

    /**
     * Display bookings for prestataires with filtering options
     */
    public function prestataireBookings(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'prestataire') {
            abort(403, 'Accès non autorisé.');
        }

        $prestataire = $user->prestataire;
        
        // Récupérer les réservations de services
        $bookingsQuery = $prestataire->bookings()->with(['client.user', 'service']);
        if ($request->filled('status') && (!$request->filled('type') || $request->type === 'bookings')) {
            $bookingsQuery->where('status', $request->status);
        }
        
        // Récupérer les locations d'équipements confirmées
        $equipmentRentalsQuery = $prestataire->equipmentRentals()->with(['client.user', 'equipment']);
        if ($request->filled('status') && (!$request->filled('type') || $request->type === 'equipment')) {
            $equipmentRentalsQuery->where('status', $request->status);
        }
        
        // Récupérer les demandes de location d'équipements
        $equipmentRentalRequestsQuery = $prestataire->equipmentRentalRequests()->with(['client.user', 'equipment']);
        if ($request->filled('status') && (!$request->filled('type') || $request->type === 'equipment')) {
            $equipmentRentalRequestsQuery->where('status', $request->status);
        }
        
        // Récupérer les ventes urgentes
        $urgentSalesQuery = $prestataire->urgentSales()->with(['contacts.user']);
        if ($request->filled('status') && (!$request->filled('type') || $request->type === 'urgent_sales')) {
            $urgentSalesQuery->where('status', $request->status);
        }
        
        // Filtrer par type si spécifié
        if ($request->filled('type')) {
            switch ($request->type) {
                case 'bookings':
                    $bookings = $bookingsQuery->latest()->paginate(10);
                    $equipmentRentals = collect();
                    $equipmentRentalRequests = collect();
                    $urgentSales = collect();
                    break;
                case 'equipment':
                    $equipmentRentals = $equipmentRentalsQuery->latest()->paginate(10);
                    $equipmentRentalRequests = $equipmentRentalRequestsQuery->latest()->get();
                    $bookings = collect();
                    $urgentSales = collect();
                    break;
                case 'urgent_sales':
                    $urgentSales = $urgentSalesQuery->latest()->paginate(10);
                    $bookings = collect();
                    $equipmentRentals = collect();
                    $equipmentRentalRequests = collect();
                    break;
                default:
                    $bookings = $bookingsQuery->latest()->take(5)->get();
                    $equipmentRentals = $equipmentRentalsQuery->latest()->take(5)->get();
                    $equipmentRentalRequests = $equipmentRentalRequestsQuery->latest()->take(5)->get();
                    $urgentSales = $urgentSalesQuery->latest()->take(5)->get();
            }
        } else {
            // Afficher tous les types avec pagination limitée
            $bookings = $bookingsQuery->latest()->take(5)->get();
            $equipmentRentals = $equipmentRentalsQuery->latest()->take(5)->get();
            $equipmentRentalRequests = $equipmentRentalRequestsQuery->latest()->take(5)->get();
            $urgentSales = $urgentSalesQuery->latest()->take(5)->get();
            
            // Si on filtre par statut sans type spécifique, vider les collections qui n'ont pas d'éléments correspondants
            if ($request->filled('status')) {
                if ($bookings->isEmpty()) {
                    $bookings = collect();
                }
                if ($equipmentRentals->isEmpty()) {
                    $equipmentRentals = collect();
                }
                if ($equipmentRentalRequests->isEmpty()) {
                    $equipmentRentalRequests = collect();
                }
                if ($urgentSales->isEmpty()) {
                    $urgentSales = collect();
                }
            }
        }

        return view('prestataire.bookings.index', compact('bookings', 'equipmentRentals', 'equipmentRentalRequests', 'urgentSales'));
    }
}
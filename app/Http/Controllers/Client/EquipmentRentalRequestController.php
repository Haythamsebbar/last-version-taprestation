<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\EquipmentRentalRequest;
use App\Models\EquipmentRental;
use App\Models\Equipment;
use App\Rules\AvailableDateRange;
use Carbon\Carbon;
use App\Notifications\NewEquipmentRentalRequestNotification;
use App\Notifications\EquipmentRentalRequestConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EquipmentRentalRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('client');
    }
    
    /**
     * Affiche la liste des demandes de location du client
     */
    public function index(Request $request)
    {
        $query = EquipmentRentalRequest::where('client_id', Auth::user()->client->id)
                                     ->with(['equipment.prestataire.user', 'equipment.categories']);
        
        // Filtrage par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filtrage par période
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case '3months':
                    $query->where('created_at', '>=', now()->subMonths(3));
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->subYear());
                    break;
            }
        }
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('equipment', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        
        // Tri
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'start_date':
                $query->orderBy('start_date', $sortOrder);
                break;
            case 'amount':
                $query->orderBy('final_amount', $sortOrder);
                break;
            case 'status':
                $query->orderBy('status', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }
        
        $requests = $query->paginate(10)->withQueryString();
        
        // Statistiques
        $stats = [
            'total' => EquipmentRentalRequest::where('client_id', Auth::user()->client->id)->count(),
            'pending' => EquipmentRentalRequest::where('client_id', Auth::user()->client->id)->where('status', 'pending')->count(),
            'accepted' => EquipmentRentalRequest::where('client_id', Auth::user()->client->id)->where('status', 'accepted')->count(),
            'confirmed' => EquipmentRentalRequest::where('client_id', Auth::user()->client->id)->where('status', 'confirmed')->count(),
            'rejected' => EquipmentRentalRequest::where('client_id', Auth::user()->client->id)->where('status', 'rejected')->count(),
            'total_amount' => EquipmentRentalRequest::where('client_id', Auth::user()->client->id)
                                                  ->whereIn('status', ['accepted', 'confirmed'])
                                                  ->sum('final_amount')
        ];
        
        return view('client.equipment-rental-requests.index', compact('requests', 'stats'));
    }

    /**
     * Enregistre une nouvelle demande de location
     */
    public function store(Request $request)
    {
        Log::info('New rental request received:', $request->all());

        try {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'delivery_required' => 'nullable|boolean',
            'delivery_address' => 'nullable|string|max:255',
            'client_message' => 'nullable|string|max:2000',
        ]);
        } catch (ValidationException $e) {
            Log::error('Validation failed for rental request', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);
            throw $e;
        }

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->startOfDay();

        // Vérifier la disponibilité
        if (!$equipment->isAvailableForPeriod($startDate, $endDate)) {
            return back()->withErrors(['start_date' => 'L\'équipement n\'est pas disponible pour la période sélectionnée.'])->withInput();
        }

        // Calcul de la durée et du coût
        $durationDays = $endDate->diffInDays($startDate) + 1;

        $rentalCost = $equipment->calculatePrice($startDate, $endDate);

        $data = array_merge($validated, [
            'client_id' => Auth::user()->client->id,
            'prestataire_id' => $equipment->prestataire_id,
            'request_number' => 'DMD-' . strtoupper(uniqid()),
            'status' => 'pending',
            'duration_days' => $durationDays,
            'unit_price' => $equipment->price_per_day, // Assuming daily rate is the unit price
            'total_amount' => $rentalCost,
            'security_deposit' => $equipment->security_deposit,
            'final_amount' => $rentalCost + (isset($validated['delivery_required']) && $validated['delivery_required'] ? $equipment->delivery_fee : 0),
            'pickup_address' => $equipment->address,
        ]);

        $rentalRequest = EquipmentRentalRequest::create($data);

        // Envoyer des notifications
        $prestataire = $equipment->prestataire;
        Notification::send($prestataire->user, new NewEquipmentRentalRequestNotification($rentalRequest));

        $client = Auth::user();
        Notification::send($client, new EquipmentRentalRequestConfirmationNotification($rentalRequest));

        return redirect()->route('client.equipment-rental-requests.show', $rentalRequest)
                         ->with('success', 'Votre demande de location a été envoyée avec succès.');
    }

    /**
     * Affiche les détails d'une demande de location
     */
    public function show(EquipmentRentalRequest $request)
    {
        // Vérifier que la demande appartient au client connecté
        // if ($request->client_id !== Auth::user()->client->id) {
        //     abort(403);
        // }
        
        $request->load([
            'equipment.prestataire.user',
            'equipment.categories',
            'rental' // Si la demande a été acceptée et confirmée
        ]);
        
        return view('client.equipment-rental-requests.show', compact('request'));
    }
    
    /**
     * Confirme une demande acceptée (paiement)
     */
    public function confirm(EquipmentRentalRequest $request)
    {
        // if ($request->client_id !== Auth::user()->client->id) {
        //     abort(403);
        // }
        
        if ($request->status !== 'accepted') {
            return back()->with('error', 'Cette demande ne peut pas être confirmée.');
        }
        
        // Vérifier que la demande n'a pas expiré
        if ($request->hasExpired()) {
            $request->update(['status' => 'expired']);
            return back()->with('error', 'Cette demande a expiré.');
        }
        
        // Vérifier la disponibilité une dernière fois
        if (!$request->equipment->isAvailableForPeriod($request->start_date, $request->end_date)) {
            return back()->with('error', 'L\'équipement n\'est plus disponible pour cette période.');
        }
        
        DB::transaction(function () use ($request) {
            // Marquer la demande comme confirmée
            $request->update([
                'status' => 'confirmed',
                'confirmed_at' => now()
            ]);
            
            // Créer la location
            $rental = EquipmentRental::create([
                'rental_number' => 'LOC-' . strtoupper(uniqid()),
                'equipment_id' => $request->equipment_id,
                'client_id' => $request->client_id,
                'prestataire_id' => $request->prestataire_id,
                'rental_request_id' => $request->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_days' => $request->duration_days,
                'unit_price' => $request->unit_price,
                'total_amount' => $request->total_amount,
                'security_deposit' => $request->security_deposit,
                'delivery_fee' => $request->delivery_fee,
                'final_amount' => $request->final_amount,
                'delivery_address' => $request->delivery_address,
                'pickup_address' => $request->pickup_address,
                'delivery_required' => $request->delivery_required,
                'pickup_required' => $request->pickup_required,
                'special_requirements' => $request->special_requirements,
                'rental_status' => 'confirmed',
                'payment_status' => 'pending'
            ]);
            
            // TODO: Intégrer le système de paiement
            // Pour l'instant, on marque comme payé
            $rental->update([
                'payment_status' => 'paid',
                'paid_at' => now()
            ]);
        });
        
        // TODO: Envoyer notifications
        
        return redirect()->route('client.equipment.rentals.show', $request->rental)
                        ->with('success', 'Votre location a été confirmée avec succès!');
    }
    
    /**
     * Annule une demande de location
     */
    public function cancel(EquipmentRentalRequest $request)
    {
        if ($request->client_id !== Auth::user()->client->id) {
            abort(403);
        }
        
        if (!in_array($request->status, ['pending', 'accepted'])) {
            return back()->with('error', 'Cette demande ne peut pas être annulée.');
        }
        
        $request->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Annulée par le client'
        ]);
        
        // TODO: Envoyer notification au prestataire
        
        return back()->with('success', 'Votre demande a été annulée.');
    }
    
    /**
     * Modifie une demande de location (si encore en attente)
     */
    public function edit(EquipmentRentalRequest $request)
    {
        if ($request->client_id !== Auth::user()->client->id) {
            abort(403);
        }
        
        if ($request->status !== 'pending') {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }
        
        $request->load(['equipment.prestataire.user', 'equipment.categories']);
        
        return view('client.equipment.requests.edit', compact('request'));
    }
    
    /**
     * Met à jour une demande de location
     */
    public function update(Request $updateRequest, EquipmentRentalRequest $rentalRequest)
    {
        if ($rentalRequest->client_id !== Auth::user()->client->id) {
            abort(403);
        }
        
        if ($rentalRequest->status !== 'pending') {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }
        
        $validated = $updateRequest->validate([
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'delivery_address' => 'nullable|string|max:500',
            'pickup_address' => 'nullable|string|max:500',
            'delivery_required' => 'boolean',
            'pickup_required' => 'boolean',
            'client_message' => 'nullable|string|max:1000',
            'special_requirements' => 'nullable|string|max:500'
        ]);
        
        // Vérifier la disponibilité
        if (!$rentalRequest->equipment->isAvailableForPeriod($validated['start_date'], $validated['end_date'], $rentalRequest->id)) {
            return back()->with('error', 'L\'équipement n\'est pas disponible pour cette période.');
        }
        
        // Recalculer les montants
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $durationDays = $startDate->diffInDays($endDate) + 1;
        
        $totalAmount = $rentalRequest->equipment->calculatePrice($durationDays);
        $deliveryFee = ($validated['delivery_required'] && !$rentalRequest->equipment->delivery_included) ? $rentalRequest->equipment->delivery_fee : 0;
        $finalAmount = $totalAmount + $deliveryFee + $rentalRequest->equipment->security_deposit;
        
        $rentalRequest->update(array_merge($validated, [
            'duration_days' => $durationDays,
            'total_amount' => $totalAmount,
            'delivery_fee' => $deliveryFee,
            'final_amount' => $finalAmount,
            'delivery_required' => $validated['delivery_required'] ?? false,
            'pickup_required' => $validated['pickup_required'] ?? false,
            'updated_at' => now()
        ]));
        
        // TODO: Envoyer notification au prestataire
        
        return redirect()->route('client.equipment.requests.show', $rentalRequest)
                        ->with('success', 'Votre demande a été mise à jour avec succès!');
    }
    
    /**
     * Supprime une demande de location
     */
    public function destroy(EquipmentRentalRequest $request)
    {
        if ($request->client_id !== Auth::user()->client->id) {
            abort(403);
        }
        
        if (!in_array($request->status, ['pending', 'rejected', 'cancelled', 'expired'])) {
            return back()->with('error', 'Cette demande ne peut pas être supprimée.');
        }
        
        $request->delete();
        
        return redirect()->route('client.equipment.requests.index')
                        ->with('success', 'La demande a été supprimée.');
    }
    
    /**
     * Exporte les demandes en CSV
     */
    public function export(Request $request)
    {
        $query = EquipmentRentalRequest::where('client_id', Auth::user()->client->id)
                                     ->with(['equipment.prestataire.user']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }
        
        $requests = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'mes_demandes_location_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function () use ($requests) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'Numéro',
                'Équipement',
                'Prestataire',
                'Date début',
                'Date fin',
                'Durée (jours)',
                'Montant total',
                'Statut',
                'Date demande',
                'Date réponse'
            ]);
            
            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->request_number,
                    $request->equipment->name,
                    $request->equipment->prestataire->user->name,
                    $request->start_date,
                    $request->end_date,
                    $request->duration_days,
                    number_format($request->final_amount, 2) . ' €',
                    ucfirst($request->status),
                    $request->created_at->format('d/m/Y H:i'),
                    $request->responded_at ? $request->responded_at->format('d/m/Y H:i') : ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Affiche les statistiques des demandes
     */
    public function stats()
    {
        $clientId = Auth::user()->client->id;
        
        // Statistiques générales
        $totalRequests = EquipmentRentalRequest::where('client_id', $clientId)->count();
        $totalAmount = EquipmentRentalRequest::where('client_id', $clientId)
                                           ->whereIn('status', ['accepted', 'confirmed'])
                                           ->sum('final_amount');
        
        // Répartition par statut
        $statusStats = EquipmentRentalRequest::where('client_id', $clientId)
                                           ->select('status', DB::raw('count(*) as count'))
                                           ->groupBy('status')
                                           ->pluck('count', 'status')
                                           ->toArray();
        
        // Évolution mensuelle
        $monthlyStats = EquipmentRentalRequest::where('client_id', $clientId)
                                            ->where('created_at', '>=', now()->subMonths(12))
                                            ->select(
                                                DB::raw('YEAR(created_at) as year'),
                                                DB::raw('MONTH(created_at) as month'),
                                                DB::raw('count(*) as count'),
                                                DB::raw('sum(final_amount) as amount')
                                            )
                                            ->groupBy('year', 'month')
                                            ->orderBy('year')
                                            ->orderBy('month')
                                            ->get();
        
        // Top équipements demandés
        $topEquipment = EquipmentRentalRequest::where('client_id', $clientId)
                                            ->with('equipment')
                                            ->select('equipment_id', DB::raw('count(*) as count'))
                                            ->groupBy('equipment_id')
                                            ->orderBy('count', 'desc')
                                            ->limit(5)
                                            ->get();
        
        // Temps de réponse moyen
        $avgResponseTime = EquipmentRentalRequest::where('client_id', $clientId)
                                                ->whereNotNull('responded_at')
                                                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, responded_at)) as avg_hours')
                                                ->value('avg_hours');
        
        return view('client.equipment.requests.stats', compact(
            'totalRequests',
            'totalAmount',
            'statusStats',
            'monthlyStats',
            'topEquipment',
            'avgResponseTime'
        ));
    }
}
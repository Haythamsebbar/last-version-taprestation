<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\EquipmentRentalRequest;
use App\Models\EquipmentRental;
use App\Http\Requests\Prestataire\CancelEquipmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EquipmentRentalRequestController extends Controller
{
    /**
     * Affiche la liste des demandes de location
     */
    public function index(Request $request)
    {
        $prestataire = Auth::user()->prestataire;
        
        $query = $prestataire->equipmentRentalRequests()
                            ->with(['equipment', 'client.user'])
                            ->latest();
        
        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('equipment')) {
            $query->where('equipment_id', $request->equipment);
        }
        
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }
        
        $requests = $query->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => $prestataire->equipmentRentalRequests()->count(),
            'pending' => $prestataire->equipmentRentalRequests()->pending()->count(),
            'accepted' => $prestataire->equipmentRentalRequests()->where('status', 'accepted')->count(),
            'rejected' => $prestataire->equipmentRentalRequests()->where('status', 'rejected')->count(),
            'expired' => $prestataire->equipmentRentalRequests()->where('status', 'expired')->count(),
        ];
        
        // Liste des équipements pour le filtre
        $equipments = $prestataire->equipment()->active()->get(['id', 'name']);
        
        return view('prestataire.equipment-rental-requests.index', compact('requests', 'stats', 'equipments'));
    }
    
    /**
     * Affiche les détails d'une demande
     */
    public function show(EquipmentRentalRequest $request)
    {
        // $this->authorize('view', $request);
        
        $request->load(['equipment', 'client.user', 'prestataire']);
        
        // Vérifier les conflits de dates
        $conflicts = $this->checkDateConflicts($request);
        
        return view('prestataire.equipment-rental-requests.show', compact('request', 'conflicts'));
    }
    
    /**
     * Accepte une demande de location
     */
    public function accept(Request $request, EquipmentRentalRequest $rentalRequest)
    {
        // $this->authorize('update', $rentalRequest);
        
        if (!$rentalRequest->isPending()) {
            return back()->with('error', 'Cette demande ne peut plus être acceptée.');
        }
        
        // Vérifier la disponibilité
        if (!$rentalRequest->equipment->isAvailableForPeriod($rentalRequest->start_date, $rentalRequest->end_date)) {
            return back()->with('error', 'L\'équipement n\'est plus disponible pour cette période.');
        }
        
        DB::transaction(function () use ($rentalRequest) {
            // Accepter la demande
            $rentalRequest->accept();
            
            // Créer la location
            $rental = EquipmentRental::create([
                'rental_number' => 'LOC-' . strtoupper(uniqid()),
                'rental_request_id' => $rentalRequest->id,
                'equipment_id' => $rentalRequest->equipment_id,
                'client_id' => $rentalRequest->client_id,
                'prestataire_id' => $rentalRequest->prestataire_id,
                'start_date' => $rentalRequest->start_date,
                'end_date' => $rentalRequest->end_date,
                'planned_duration_days' => $rentalRequest->duration_days ?? 1,
                'unit_price' => $rentalRequest->unit_price ?? 0,
                'base_amount' => $rentalRequest->total_amount ?? 0,
                'security_deposit' => $rentalRequest->security_deposit ?? 0,
                'delivery_fee' => $rentalRequest->delivery_fee ?? 0,
                'total_amount' => $rentalRequest->total_amount ?? 0,
                'final_amount' => $rentalRequest->final_amount ?? ($rentalRequest->total_amount + ($rentalRequest->delivery_fee ?? 0)),
                'delivery_address' => $rentalRequest->delivery_address,
                'pickup_address' => $rentalRequest->pickup_address,
                'status' => 'confirmed',
                'payment_status' => 'pending'
            ]);
            
            // Mettre à jour le statut de l'équipement si nécessaire
            if ($rentalRequest->equipment->status === 'active') {
                $rentalRequest->equipment->update(['status' => 'rented']);
            }
        });
        
        // TODO: Envoyer notification au client
        
        return redirect()->route('prestataire.equipment-rental-requests.show', $rentalRequest)
                        ->with('success', 'Demande acceptée avec succès! La location a été créée.');
    }
    
    /**
     * Rejette une demande de location
     */
    public function reject(Request $request, EquipmentRentalRequest $rentalRequest)
    {
        // $this->authorize('update', $rentalRequest);
        
        if (!$rentalRequest->isPending()) {
            return back()->with('error', 'Cette demande ne peut plus être rejetée.');
        }
        
        $rentalRequest->reject($request->input('rejection_reason'));
        
        // TODO: Envoyer notification au client
        
        return redirect()->route('prestataire.equipment-rental-requests.show', $rentalRequest)
                        ->with('success', 'Demande rejetée.');
    }
    
    /**
     * Annule une demande acceptée (avant confirmation)
     */
    public function cancel(CancelEquipmentRequest $request, EquipmentRentalRequest $rentalRequest)
    {
        // $this->authorize('update', $rentalRequest);

        if (!in_array($rentalRequest->status, ['accepted', 'confirmed'])) {
            return back()->with('error', 'Cette demande ne peut pas être annulée.');
        }
        
        DB::transaction(function () use ($rentalRequest, $request) {
            // Annuler la demande
            $rentalRequest->cancel($request->validated()['cancellation_reason']);
            
            // Annuler la location associée si elle existe
            if ($rentalRequest->rental) {
                $rentalRequest->rental->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $request->validated()['cancellation_reason'],
                    'cancelled_at' => now(),
                    'cancelled_by' => Auth::id()
                ]);
            }
            
            // Remettre l'équipement disponible
            if ($rentalRequest->equipment->status === 'rented') {
                $rentalRequest->equipment->update(['status' => 'active']);
            }
        });
        
        // TODO: Envoyer notification au client
        
        return redirect()->route('prestataire.equipment-rental-requests.show', $rentalRequest)
                        ->with('success', 'Demande annulée.');
    }
    
    /**
     * Répond à une demande avec un message
     */
    public function respond(RespondToEquipmentRequest $request, EquipmentRentalRequest $rentalRequest)
    {
        $this->authorize('update', $rentalRequest);

        $rentalRequest->update([
            'prestataire_response' => $request->validated()['response_message'],
            'responded_at' => now()
        ]);
        
        // TODO: Envoyer notification au client
        
        return back()->with('success', 'Réponse envoyée au client.');
    }
    
    /**
     * Marque une demande comme expirée
     */
    public function markExpired(EquipmentRentalRequest $request)
    {
        $this->authorize('update', $request);
        
        if (!$request->isPending()) {
            return back()->with('error', 'Cette demande ne peut pas être marquée comme expirée.');
        }
        
        $request->expire();
        
        return back()->with('success', 'Demande marquée comme expirée.');
    }
    
    /**
     * Exporte les demandes en CSV
     */
    public function export(Request $request)
    {
        $prestataire = Auth::user()->prestataire;
        
        $query = $prestataire->equipmentRentalRequests()
                            ->with(['equipment', 'client.user']);
        
        // Appliquer les mêmes filtres que l'index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('equipment')) {
            $query->where('equipment_id', $request->equipment);
        }
        
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }
        
        $requests = $query->get();
        
        $filename = 'demandes_location_' . date('Y-m-d') . '.csv';
        
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
                'Client',
                'Date début',
                'Date fin',
                'Durée (jours)',
                'Montant total',
                'Statut',
                'Date demande',
                'Date réponse'
            ]);
            
            // Données
            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->request_number,
                    $request->equipment->name,
                    $request->client->user->name,
                    $request->start_date->format('d/m/Y'),
                    $request->end_date->format('d/m/Y'),
                    $request->duration_days,
                    number_format($request->final_amount, 2) . ' €',
                    $request->formatted_status,
                    $request->created_at->format('d/m/Y H:i'),
                    $request->responded_at ? $request->responded_at->format('d/m/Y H:i') : ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Vérifie les conflits de dates pour une demande
     */
    private function checkDateConflicts(EquipmentRentalRequest $request)
    {
        return $request->equipment->rentals()
                      ->where('id', '!=', $request->id)
                      ->whereIn('status', ['confirmed', 'in_preparation', 'delivered', 'in_use'])
                      ->where(function ($query) use ($request) {
                          $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                                ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                                ->orWhere(function ($q) use ($request) {
                                    $q->where('start_date', '<=', $request->start_date)
                                      ->where('end_date', '>=', $request->end_date);
                                });
                      })
                      ->with('client.user')
                      ->get();
    }
}
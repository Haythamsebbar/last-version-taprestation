<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prestataire;
use App\Models\PrestataireAvailability;
use App\Models\AvailabilityException;
use App\Models\TimeSlot;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    /**
     * Affiche la page de gestion des disponibilités
     */
    public function index()
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;

        // Récupérer les disponibilités hebdomadaires
        $weeklyAvailability = PrestataireAvailability::where('prestataire_id', $prestataire->id)
            ->get();

        // Si aucune disponibilité n'est configurée, on en crée par défaut
        if ($weeklyAvailability->isEmpty()) {
            // Créer une disponibilité par défaut pour chaque jour de la semaine (1=Lundi, ..., 7=Dimanche)
            for ($i = 1; $i <= 7; $i++) {
                PrestataireAvailability::create([
                    'prestataire_id' => $prestataire->id,
                    'day_of_week' => $i % 7, // 1=Lundi, ..., 6=Samedi, 0=Dimanche
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'slot_duration' => 60,
                    'is_active' => false,
                ]);
            }
            // On recharge les disponibilités
            $weeklyAvailability = PrestataireAvailability::where('prestataire_id', $prestataire->id)
                ->get();
        }

        // Trier par jour de la semaine, en s'assurant que Lundi (1) vient en premier.
        $weeklyAvailability = $weeklyAvailability->sortBy(function ($item) {
            // Traite le dimanche (0) comme le 7ème jour pour le tri
            return $item->day_of_week == 0 ? 7 : $item->day_of_week;
        });

        // Récupérer les exceptions de disponibilité à venir
        $exceptions = AvailabilityException::where('prestataire_id', $prestataire->id)
            ->where('date', '>=', now()->startOfDay())
            ->orderBy('date')
            ->get();

        // Récupérer les paramètres de réservation
        $bookingSettings = $prestataire->bookingSettings ?? null;

        return view('prestataire.availability.index', compact(
            'prestataire',
            'weeklyAvailability',
            'exceptions',
            'bookingSettings'
        ));
    }
    
    /**
     * Affiche le calendrier de disponibilité
     */
    public function calendar(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        // Paramètres de vue par défaut
        $view = $request->get('view', 'month'); // month, week
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::now();
        
        return view('prestataire.availability.calendar', compact(
            'prestataire',
            'view',
            'date'
        ));
    }
    
    /**
     * API pour récupérer les événements du calendrier de disponibilité
     */
    public function events(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        $start = Carbon::parse($request->get('start'));
        $end = Carbon::parse($request->get('end'));
        
        // Récupérer les disponibilités hebdomadaires
        $weeklyAvailability = PrestataireAvailability::where('prestataire_id', $prestataire->id)
            ->where('is_active', true)
            ->get();
        
        // Récupérer les exceptions de disponibilité
        $exceptions = AvailabilityException::where('prestataire_id', $prestataire->id)
            ->where('is_active', true)
            ->whereBetween('date', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get();
        
        // Récupérer les réservations existantes
        $bookings = TimeSlot::where('prestataire_id', $prestataire->id)
            ->whereIn('status', ['booked', 'pending'])
            ->whereBetween('start_datetime', [$start, $end])
            ->get();
        
        $events = [];
        
        // Ajouter les disponibilités hebdomadaires
        for ($day = $start->copy(); $day <= $end; $day->addDay()) {
            $dayOfWeek = $day->dayOfWeek;
            $dayAvailability = $weeklyAvailability->where('day_of_week', $dayOfWeek)->first();
            
            if ($dayAvailability && $dayAvailability->is_active) {
                // Vérifier s'il y a une exception pour ce jour
                $dayException = $exceptions->first(function ($exception) use ($day) {
                    return $exception->date->isSameDay($day);
                });
                
                if (!$dayException || ($dayException && $dayException->isCustomHours())) {
                    // Utiliser les heures personnalisées de l'exception si disponibles
                    if ($dayException && $dayException->isCustomHours()) {
                        $startTime = $day->copy()->setTimeFromTimeString($dayException->start_time);
                        $endTime = $day->copy()->setTimeFromTimeString($dayException->end_time);
                    } else {
                        $startTime = $day->copy()->setTimeFromTimeString($dayAvailability->start_time);
                        $endTime = $day->copy()->setTimeFromTimeString($dayAvailability->end_time);
                    }
                    
                    $events[] = [
                        'id' => 'avail_' . $day->format('Y-m-d') . '_' . $dayAvailability->id,
                        'title' => 'Disponible',
                        'start' => $startTime->toISOString(),
                        'end' => $endTime->toISOString(),
                        'backgroundColor' => '#10b981', // vert
                        'borderColor' => '#10b981',
                        'textColor' => '#ffffff',
                        'allDay' => false,
                        'extendedProps' => [
                            'type' => 'availability'
                        ]
                    ];
                    
                    // Ajouter la pause si définie
                    if ($dayAvailability->hasBreak() && !($dayException && $dayException->isCustomHours())) {
                        $breakStart = $day->copy()->setTimeFromTimeString($dayAvailability->break_start_time);
                        $breakEnd = $day->copy()->setTimeFromTimeString($dayAvailability->break_end_time);
                        
                        $events[] = [
                            'id' => 'break_' . $day->format('Y-m-d') . '_' . $dayAvailability->id,
                            'title' => 'Pause',
                            'start' => $breakStart->toISOString(),
                            'end' => $breakEnd->toISOString(),
                            'backgroundColor' => '#f59e0b', // orange
                            'borderColor' => '#f59e0b',
                            'textColor' => '#ffffff',
                            'allDay' => false,
                            'extendedProps' => [
                                'type' => 'break'
                            ]
                        ];
                    }
                }
            }
        }
        
        // Ajouter les exceptions (jours non disponibles, vacances, etc.)
        foreach ($exceptions as $exception) {
            if (!$exception->isCustomHours()) { // Les heures personnalisées sont déjà traitées ci-dessus
                $events[] = [
                    'id' => 'exception_' . $exception->id,
                    'title' => $exception->reason ?: 'Non disponible',
                    'start' => $exception->date->toDateString(),
                    'backgroundColor' => '#ef4444', // rouge
                    'borderColor' => '#ef4444',
                    'textColor' => '#ffffff',
                    'allDay' => true,
                    'extendedProps' => [
                        'type' => 'exception',
                        'exceptionType' => $exception->type,
                        'reason' => $exception->reason
                    ]
                ];
            }
        }
        
        // Ajouter les réservations
        foreach ($bookings as $booking) {
            $color = $booking->status === 'pending' ? '#f59e0b' : '#3b82f6'; // orange pour pending, bleu pour booked
            
            $events[] = [
                'id' => 'booking_' . $booking->id,
                'title' => 'Réservé',
                'start' => $booking->start_datetime->toISOString(),
                'end' => $booking->end_datetime->toISOString(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'allDay' => false,
                'extendedProps' => [
                    'type' => 'booking',
                    'status' => $booking->status
                ]
            ];
        }
        
        return response()->json($events);
    }
    
    /**
     * Met à jour les disponibilités hebdomadaires
     */
    public function updateWeeklyAvailability(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;

        $daysData = $request->input('days', []);

        foreach ($daysData as $dayOfWeek => $data) {
            $availability = PrestataireAvailability::where('prestataire_id', $prestataire->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            if ($availability) {
                $availability->update([
                    'is_active' => isset($data['is_active']),
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'slot_duration' => $data['slot_duration'],
                ]);
            }
        }

        return redirect()->route('prestataire.availability.index')
            ->with('success', 'Vos disponibilités ont été mises à jour avec succès.');
    }
    
    /**
     * Ajoute une exception de disponibilité
     */
    public function addException(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'type' => 'required|in:unavailable,holiday,vacation,sick_leave,custom_hours,blocked',
            'start_time' => 'required_if:type,custom_hours|nullable|date_format:H:i',
            'end_time' => 'required_if:type,custom_hours|nullable|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'required_if:is_recurring,true|nullable|array',
        ]);
        
        $exception = new AvailabilityException([
            'prestataire_id' => $prestataire->id,
            'date' => $request->input('date'),
            'type' => $request->input('type'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'reason' => $request->input('reason'),
            'is_recurring' => $request->input('is_recurring', false),
            'recurrence_pattern' => $request->input('recurrence_pattern'),
            'is_active' => true,
        ]);
        
        $exception->save();
        
        // Si c'est une journée complète non disponible, annuler les réservations existantes
        if (in_array($request->input('type'), ['unavailable', 'holiday', 'vacation', 'sick_leave', 'blocked'])) {
            $date = Carbon::parse($request->input('date'));
            
            $slots = TimeSlot::where('prestataire_id', $prestataire->id)
                ->whereDate('start_datetime', $date)
                ->whereIn('status', ['available', 'pending'])
                ->get();
            
            foreach ($slots as $slot) {
                if ($slot->status === 'available') {
                    $slot->update(['status' => 'blocked']);
                } elseif ($slot->status === 'pending') {
                    // Notifier le client que sa réservation est annulée
                    $booking = $slot->booking;
                    if ($booking) {
                        $booking->cancel('Le prestataire n\'est pas disponible à cette date.');
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Exception de disponibilité ajoutée avec succès',
            'exception' => $exception
        ]);
    }
    
    /**
     * Supprime une exception de disponibilité
     */
    public function deleteException(AvailabilityException $exception)
    {
        $user = Auth::user();
        
        if ($exception->prestataire_id !== $user->prestataire->id) {
            abort(403, 'Non autorisé');
        }
        
        $exception->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Exception de disponibilité supprimée avec succès'
        ]);
    }
    
    /**
     * Met à jour les paramètres de réservation
     */
    public function updateBookingSettings(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        $request->validate([
            'requires_approval' => 'required|boolean',
            'min_advance_hours' => 'required|integer|min:0',
            'max_advance_days' => 'required|integer|min:1',
            'buffer_between_appointments' => 'required|integer|min:0',
        ]);
        
        $prestataire->update([
            'requires_approval' => $request->input('requires_approval'),
            'min_advance_hours' => $request->input('min_advance_hours'),
            'max_advance_days' => $request->input('max_advance_days'),
            'buffer_between_appointments' => $request->input('buffer_between_appointments'),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Paramètres de réservation mis à jour avec succès'
        ]);
    }
    
    /**
     * Génère les créneaux horaires disponibles pour une période donnée
     */
    public function generateTimeSlots(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'service_id' => 'required|exists:services,id',
        ]);
        
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $serviceId = $request->input('service_id');
        
        // Vérifier que le service appartient au prestataire
        $service = $prestataire->services()->findOrFail($serviceId);
        
        // Récupérer les disponibilités hebdomadaires
        $weeklyAvailability = PrestataireAvailability::where('prestataire_id', $prestataire->id)
            ->where('is_active', true)
            ->get();
        
        // Récupérer les exceptions de disponibilité
        $exceptions = AvailabilityException::where('prestataire_id', $prestataire->id)
            ->where('is_active', true)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        
        $generatedSlots = [];
        
        // Pour chaque jour de la période
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek;
            $dayAvailability = $weeklyAvailability->where('day_of_week', $dayOfWeek)->first();
            
            // Vérifier si le jour est disponible
            if ($dayAvailability) {
                // Vérifier s'il y a une exception pour ce jour
                $dayException = $exceptions->first(function ($exception) use ($date) {
                    return $exception->date->isSameDay($date);
                });
                
                // Si le jour est bloqué par une exception, passer au jour suivant
                if ($dayException && !$dayException->isCustomHours()) {
                    continue;
                }
                
                // Utiliser les heures personnalisées de l'exception si disponibles
                if ($dayException && $dayException->isCustomHours()) {
                    $startTime = $date->copy()->setTimeFromTimeString($dayException->start_time);
                    $endTime = $date->copy()->setTimeFromTimeString($dayException->end_time);
                    $slotDuration = $dayAvailability->slot_duration; // Utiliser la durée de créneau standard
                } else {
                    $startTime = $date->copy()->setTimeFromTimeString($dayAvailability->start_time);
                    $endTime = $date->copy()->setTimeFromTimeString($dayAvailability->end_time);
                    $slotDuration = $dayAvailability->slot_duration;
                }
                
                // Générer les créneaux pour ce jour
                $currentTime = $startTime->copy();
                
                while ($currentTime->copy()->addMinutes($slotDuration) <= $endTime) {
                    $slotEnd = $currentTime->copy()->addMinutes($slotDuration);
                    
                    // Vérifier si le créneau chevauche une pause
                    $skipSlot = false;
                    
                    if ($dayAvailability->hasBreak() && !($dayException && $dayException->isCustomHours())) {
                        $breakStart = $date->copy()->setTimeFromTimeString($dayAvailability->break_start_time);
                        $breakEnd = $date->copy()->setTimeFromTimeString($dayAvailability->break_end_time);
                        
                        if ($currentTime < $breakEnd && $slotEnd > $breakStart) {
                            $skipSlot = true;
                        }
                    }
                    
                    // Vérifier si le créneau chevauche une réservation existante
                    $existingSlot = TimeSlot::where('prestataire_id', $prestataire->id)
                        ->where(function ($query) use ($currentTime, $slotEnd) {
                            $query->whereBetween('start_datetime', [$currentTime, $slotEnd])
                                ->orWhereBetween('end_datetime', [$currentTime, $slotEnd])
                                ->orWhere(function ($q) use ($currentTime, $slotEnd) {
                                    $q->where('start_datetime', '<=', $currentTime)
                                      ->where('end_datetime', '>=', $slotEnd);
                                });
                        })
                        ->exists();
                    
                    if (!$skipSlot && !$existingSlot) {
                        // Créer le créneau
                        $slot = TimeSlot::create([
                            'prestataire_id' => $prestataire->id,
                            'service_id' => $service->id,
                            'start_datetime' => $currentTime,
                            'end_datetime' => $slotEnd,
                            'status' => 'available',
                            // 'price' => $service->price, // Supprimé pour des raisons de confidentialité
                            'requires_approval' => $service->requires_approval ?? $prestataire->requires_approval ?? false,
                        ]);
                        
                        $generatedSlots[] = $slot;
                    }
                    
                    $currentTime = $slotEnd;
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => count($generatedSlots) . ' créneaux horaires générés avec succès',
            'slots' => $generatedSlots
        ]);
    }
}
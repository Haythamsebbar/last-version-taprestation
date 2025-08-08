<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Affiche la liste des notifications.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Notification::with(['notifiable']);
        
        // Filtrage par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filtrage par statut de lecture
        if ($request->filled('read_status')) {
            if ($request->read_status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->read_status === 'unread') {
                $query->whereNull('read_at');
            }
        }
        
        // Filtrage par date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Recherche par contenu
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('data', 'like', '%' . $search . '%');
            });
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Notification::count(),
            'unread' => Notification::whereNull('read_at')->count(),
            'read' => Notification::whereNotNull('read_at')->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
        ];
        
        // Types de notifications pour le filtre
        $notificationTypes = Notification::select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->map(function($type) {
                return [
                    'value' => $type,
                    'label' => $this->getNotificationTypeLabel($type)
                ];
            });
        
        return view('admin.notifications.index-modern', [
            'notifications' => $notifications,
            'stats' => $stats,
            'notificationTypes' => $notificationTypes,
        ]);
    }
    
    /**
     * Affiche les détails d'une notification.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $notification = Notification::with(['notifiable'])->findOrFail($id);
        
        return view('admin.notifications.show', [
            'notification' => $notification,
        ]);
    }
    
    /**
     * Marque une notification comme lue.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }
    
    /**
     * Marque toutes les notifications comme lues.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        Notification::whereNull('read_at')->update(['read_at' => now()]);
        
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
    
    /**
     * Supprime une notification.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return redirect()->route('administrateur.notifications.index')
            ->with('success', 'La notification a été supprimée avec succès.');
    }
    
    /**
     * Supprime les notifications anciennes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);
        
        $cutoffDate = now()->subDays($request->days);
        $deletedCount = Notification::where('created_at', '<', $cutoffDate)->delete();
        
        return redirect()->back()
            ->with('success', "$deletedCount notifications anciennes ont été supprimées.");
    }
    
    /**
     * Envoie une notification personnalisée.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendCustom(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:all,role,specific',
            'role' => 'required_if:recipient_type,role|in:client,prestataire,administrateur',
            'user_ids' => 'required_if:recipient_type,specific|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|string|max:100',
        ]);
        
        $recipients = collect();
        
        switch ($request->recipient_type) {
            case 'all':
                $recipients = User::all();
                break;
            case 'role':
                $recipients = User::where('role', $request->role)->get();
                break;
            case 'specific':
                $recipients = User::whereIn('id', $request->user_ids)->get();
                break;
        }
        
        $notificationData = [
            'title' => $request->title,
            'message' => $request->message,
            'admin_sender' => Auth::user()->name,
        ];
        
        foreach ($recipients as $user) {
            $user->notifications()->create([
                'id' => \Str::uuid(),
                'type' => $request->type,
                'data' => $notificationData,
                'created_at' => now(),
            ]);
        }
        
        return redirect()->back()
            ->with('success', "Notification envoyée à {$recipients->count()} utilisateur(s).");
    }
    
    /**
     * Affiche les statistiques des notifications.
     *
     * @return \Illuminate\View\View
     */
    public function analytics()
    {
        // Statistiques par type
        $typeStats = Notification::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function($stat) {
                return [
                    'type' => $this->getNotificationTypeLabel($stat->type),
                    'count' => $stat->count
                ];
            });
        
        // Statistiques par jour (7 derniers jours)
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyStats[] = [
                'date' => $date->format('d/m'),
                'count' => Notification::whereDate('created_at', $date)->count(),
                'read' => Notification::whereDate('created_at', $date)
                    ->whereNotNull('read_at')
                    ->count(),
            ];
        }
        
        // Taux de lecture
        $totalNotifications = Notification::count();
        $readNotifications = Notification::whereNotNull('read_at')->count();
        $readRate = $totalNotifications > 0 ? round(($readNotifications / $totalNotifications) * 100, 1) : 0;
        
        return view('admin.notifications.analytics', [
            'typeStats' => $typeStats,
            'dailyStats' => $dailyStats,
            'readRate' => $readRate,
        ]);
    }
    
    /**
     * Obtient le libellé d'un type de notification.
     *
     * @param  string  $type
     * @return string
     */
    private function getNotificationTypeLabel($type)
    {
        $labels = [
            'App\\Notifications\\NewOfferNotification' => 'Nouvelle offre',
            'App\\Notifications\\OfferAcceptedNotification' => 'Offre acceptée',
            'App\\Notifications\\OfferRejectedNotification' => 'Offre rejetée',
            'App\\Notifications\\BookingCancelledNotification' => 'Réservation annulée',
            'App\\Notifications\\MissionCompletedNotification' => 'Mission terminée',
            'App\\Notifications\\NewReviewNotification' => 'Nouvel avis',
            'App\\Notifications\\PrestataireApprovedNotification' => 'Prestataire approuvé',
            'App\\Notifications\\RequestHasOffersNotification' => 'Demande avec offres',
            'App\\Notifications\\NewMessageNotification' => 'Nouveau message',
            'App\\Notifications\\NewClientRequestNotification' => 'Demande client reçue',
            'App\\Notifications\\AnnouncementStatusNotification' => 'Statut d\'annonce',
            'App\\Notifications\\NewBookingNotification' => 'Nouvelle réservation',
            'App\\Notifications\\BookingConfirmedNotification' => 'Réservation confirmée',
        ];
        
        return $labels[$type] ?? $type;
    }
}
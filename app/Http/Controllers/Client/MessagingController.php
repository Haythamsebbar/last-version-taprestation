<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessagingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:client']);
    }

    /**
     * Affiche la liste des conversations du client.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Récupérer les ID des utilisateurs avec qui le client a conversé
        $participantIds = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->pluck('sender_id')
            ->merge(Message::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->pluck('receiver_id'))
            ->unique()
            ->reject(function ($id) use ($user) {
                return $id == $user->id;
            });

        $conversations = User::whereIn('id', $participantIds)
            ->where('role', 'prestataire') // S'assurer que ce sont des prestataires
            ->get()
            ->map(function ($otherUser) use ($user) {
                $lastMessage = Message::where(function ($query) use ($user, $otherUser) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $otherUser->id);
                })->orWhere(function ($query) use ($user, $otherUser) {
                    $query->where('sender_id', $otherUser->id)->where('receiver_id', $user->id);
                })->latest()->first();

                $unreadCount = Message::where('sender_id', $otherUser->id)
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->sortByDesc(function ($conversation) {
                return $conversation['last_message'] ? $conversation['last_message']->created_at : 0;
            });

        $totalUnreadCount = $conversations->sum('unread_count');

        return view('client.messaging.index', [
            'conversations' => $conversations,
            'totalUnreadCount' => $totalUnreadCount,
        ]);
    }

    /**
     * Affiche une conversation spécifique.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $userId)
    {
        $user = Auth::user();
        $otherUser = User::findOrFail($userId);
        $serviceId = $request->query('service_id');
        $prefilledMessage = '';

        if ($serviceId) {
            $service = \App\Models\Service::find($serviceId);
            if ($service && $service->prestataire_id === $otherUser->prestataire->id) {
                $prefilledMessage = "Bonjour, je suis intéressé par votre service \"{$service->title}\". Pouvez-vous me donner plus d’informations à ce sujet ?";
            }
        }

        // Vérifier que l'autre utilisateur est un prestataire
        if ($otherUser->role !== 'prestataire') {
            abort(403, 'Vous ne pouvez converser qu\'avec des prestataires.');
        }

        // Récupérer tous les messages entre ces deux utilisateurs
        $messages = Message::where(function ($query) use ($user, $otherUser) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $otherUser->id);
        })
            ->orWhere(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $otherUser->id)
                    ->where('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer les messages reçus comme lus
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('client.messaging.show', [
            'messages' => $messages,
            'otherUser' => $otherUser,
            'prefilledMessage' => $prefilledMessage
        ]);
    }

    /**
     * Envoie un nouveau message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $userId)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        $receiver = User::findOrFail($userId);
        
        // Vérifier que le destinataire est un prestataire
        if ($receiver->role !== 'prestataire') {
            return back()->withErrors(['error' => 'Vous ne pouvez envoyer des messages qu\'aux prestataires.']);
        }

        // Créer le message
        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'content' => $request->content
        ]);

        return back()->with('success', 'Message envoyé avec succès.');
    }

    /**
     * Démarre une nouvelle conversation avec un prestataire.
     *
     * @param  int  $prestataireId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startConversation($prestataireId)
    {
        $prestataire = User::where('id', $prestataireId)
            ->where('role', 'prestataire')
            ->firstOrFail();

        return redirect()->route('client.messaging.show', $prestataire->id);
    }
}
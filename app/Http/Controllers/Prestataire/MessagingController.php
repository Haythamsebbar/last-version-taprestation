<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:prestataire']);
    }

    /**
     * Affiche la liste des conversations du prestataire.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier que le prestataire est approuvé
        if (!$user->prestataire || !$user->prestataire->is_approved) {
            return redirect()->route('prestataire.dashboard')
                ->with('error', 'Vous devez être un prestataire validé pour accéder à la messagerie.');
        }
        
        // Récupérer toutes les conversations du prestataire
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver', 'clientRequest'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($user) {
                // Grouper par l'autre participant de la conversation
                return $message->sender_id == $user->id ? $message->receiver_id : $message->sender_id;
            })
            ->map(function ($messages) {
                return $messages->first(); // Prendre le message le plus récent de chaque conversation
            });

        // Compter les messages non lus
        $unreadCount = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('prestataire.messaging.index', [
            'conversations' => $conversations,
            'unreadCount' => $unreadCount
        ]);
    }

    /**
     * Affiche une conversation spécifique.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function show($userId)
    {
        $user = Auth::user();
        
        // Vérifier que le prestataire est approuvé
        if (!$user->prestataire || !$user->prestataire->is_approved) {
            return redirect()->route('prestataire.dashboard')
                ->with('error', 'Vous devez être un prestataire validé pour accéder à la messagerie.');
        }
        
        $otherUser = User::findOrFail($userId);
        
        // Vérifier que l'autre utilisateur est un client
        if ($otherUser->role !== 'client') {
            abort(403, 'Vous ne pouvez converser qu\'avec des clients.');
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
            ->with(['sender', 'receiver', 'clientRequest'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer les messages reçus comme lus
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('prestataire.messaging.show', [
            'messages' => $messages,
            'otherUser' => $otherUser
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
            'content' => 'required|string|max:1000',
            'client_request_id' => 'nullable|exists:client_requests,id'
        ]);

        $user = Auth::user();
        
        // Vérifier que le prestataire est approuvé
        if (!$user->prestataire || !$user->prestataire->is_approved) {
            return back()->withErrors(['error' => 'Vous devez être un prestataire validé pour envoyer des messages.']);
        }
        
        $receiver = User::findOrFail($userId);
        
        // Vérifier que le destinataire est un client
        if ($receiver->role !== 'client') {
            return back()->withErrors(['error' => 'Vous ne pouvez envoyer des messages qu\'aux clients.']);
        }

        // Créer le message
        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'client_request_id' => $request->client_request_id,
            'content' => $request->content
        ]);

        return back()->with('success', 'Message envoyé avec succès.');
    }

    /**
     * Démarre une nouvelle conversation avec un client depuis une demande.
     *
     * @param  int  $clientRequestId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startConversationFromRequest($clientRequestId)
    {
        $clientRequest = ClientRequest::findOrFail($clientRequestId);
        $client = $clientRequest->client;

        return redirect()->route('prestataire.messaging.show', $client->user->id);
    }
}
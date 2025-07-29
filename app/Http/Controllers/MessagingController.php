<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MessagingController extends Controller
{
    /**
     * Affiche la liste des conversations de l'utilisateur.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('messaging.index');
    }

    /**
     * Affiche une conversation spécifique.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Vérifier que l'utilisateur peut converser avec l'autre utilisateur
        if ($currentUser->isClient() && $user->role !== 'prestataire') {
            abort(403, 'Vous ne pouvez converser qu\'avec des prestataires.');
        } elseif ($currentUser->isPrestataire() && $user->role !== 'client') {
            abort(403, 'Vous ne pouvez converser qu\'avec des clients.');
        }

        // Récupérer tous les messages entre ces deux utilisateurs
        $messages = Message::where(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $currentUser->id)
                      ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $currentUser->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer les messages reçus comme lus
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messaging.conversation', [
            'messages' => $messages,
            'otherUser' => $user
        ]);
    }

    /**
     * Envoie un nouveau message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $currentUser = Auth::user();
        
        // Vérifier que l'utilisateur peut envoyer un message à l'autre utilisateur
        if ($currentUser->isClient() && $user->role !== 'prestataire') {
            return back()->withErrors(['error' => 'Vous ne pouvez envoyer des messages qu\'aux prestataires.']);
        } elseif ($currentUser->isPrestataire() && $user->role !== 'client') {
            return back()->withErrors(['error' => 'Vous ne pouvez envoyer des messages qu\'aux clients.']);
        }

        // Créer le message
        Message::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $user->id,
            'content' => $request->content,
            'type' => 'text'
        ]);

        return back()->with('success', 'Message envoyé avec succès.');
    }
    
    /**
     * Traite les fichiers joints aux messages
     */
    private function processMessageFile($request, $message)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filename = 'file_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('messages/files', $filename, 'public');
            
            $message->file_name = $originalName;
            $message->file_type = $file->getMimeType();
            $message->file_size = $file->getSize();
        }
        
        return $message;
    }
        
    /**
     * Démarre une nouvelle conversation avec un prestataire.
     *
     * @param  \App\Models\User  $prestataire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startConversation(User $prestataire)
    {
        $currentUser = Auth::user();
        
        // Vérifier que l'utilisateur est un client et que le destinataire est un prestataire
        if (!$currentUser->isClient() || $prestataire->role !== 'prestataire') {
            abort(403, 'Action non autorisée.');
        }

        return redirect()->route('messaging.show', $prestataire);
    }
    
    /**
     * Met à jour un message (édition)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $message = Message::findOrFail($id);
        
        // Vérifier que l'utilisateur est l'auteur du message
        if ($message->sender_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }
        
        // Seuls les messages texte peuvent être édités
        if ($message->type !== 'text') {
            return response()->json(['success' => false, 'message' => 'Seuls les messages texte peuvent être édités'], 422);
        }
        
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:10000',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $message->content = $request->content;
        $message->edited_at = now();
        $message->save();
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    /**
     * Supprime un message
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $message = Message::findOrFail($id);
        
        // Vérifier que l'utilisateur est l'auteur du message
        if ($message->sender_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }
        
        // Supprimer les fichiers associés si nécessaire
        if (in_array($message->type, ['voice', 'file']) && $message->file_path) {
            Storage::disk('public')->delete($message->file_path);
        }
        
        $message->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Message supprimé avec succès'
        ]);
    }
    

    
    /**
     * Marque tous les messages d'une conversation comme lus
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        if ($request->recipient_id) {
            // Marquer les messages d'une conversation individuelle comme lus
            Message::where('sender_id', $request->recipient_id)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Messages marqués comme lus'
        ]);
    }
    
    /**
     * Initialise une session de visioconférence
     */
    public function startVideoCall(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Générer un ID de salle unique
        $roomId = Str::uuid()->toString();
        
        // Créer un message de type appel vidéo
        $message = new Message();
        $message->sender_id = $user->id;
        $message->receiver_id = $request->recipient_id;
        $message->type = 'video_call';
        
        $videoCallData = [
            'room_id' => $roomId,
            'status' => 'ongoing',
            'start_time' => now()->toIso8601String(),
            'participants' => [
                [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'role' => 'initiator'
                ]
            ]
        ];
        
        $message->video_call_data = json_encode($videoCallData);
        $message->save();
        
        return response()->json([
            'success' => true,
            'room_id' => $roomId,
            'message_id' => $message->id
        ]);
    }
    
    /**
     * Recherche des utilisateurs pour ajouter à une conversation
     */
    public function searchUsers(Request $request)
    {
        $user = Auth::user();
        $query = $request->input('query');
        
        $users = User::where('id', '!=', $user->id)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'avatar']);
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
}
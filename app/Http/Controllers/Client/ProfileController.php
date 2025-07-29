<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
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
     * Affiche le formulaire d'édition du profil.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        $client = $user->client;
        
        return view('client.profile.edit', [
            'user' => $user,
            'client' => $client
        ]);
    }

    /**
     * Met à jour le profil du client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Mise à jour des informations utilisateur
        $user->name = $request->name;
        $user->email = $request->email;
        
        // Mise à jour du mot de passe si fourni
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }
        
        $user->save();

        // Mise à jour ou création du profil client
        if (!$client) {
            $client = new Client();
            $client->user_id = $user->id;
        }
        
        $client->phone = $request->phone;
        $client->address = $request->address;
        $client->bio = $request->bio;
        
        // Gestion de l'avatar
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar s'il existe
            if ($client->avatar && Storage::disk('public')->exists($client->avatar)) {
                Storage::disk('public')->delete($client->avatar);
            }
            
            // Stocker le nouvel avatar
            $avatarPath = $request->file('avatar')->store('avatars/clients', 'public');
            $client->avatar = $avatarPath;
        }
        
        $client->save();

        return redirect()->route('client.profile.edit')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Affiche le profil public du client.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();
        $client = $user->client;
        
        // Récupérer les demandes récentes
        $recentRequests = $user->clientRequests()->orderBy('created_at', 'desc')->take(5)->get();
        
        // Récupérer les avis reçus
        $reviews = $client ? $client->reviews()->with(['prestataire.user', 'service'])->latest()->get() : collect([]);
        
        // Statistiques du client
        $stats = [
            'total_requests' => $user->clientRequests()->count(),
            'completed_requests' => $user->clientRequests()->where('status', 'completed')->count(),
            'following_count' => $client ? $client->followedPrestataires()->count() : 0,
            'average_rating' => $reviews->avg('rating'),
            'member_since' => $user->created_at->format('F Y')
        ];
        
        return view('client.profile.show', [
            'user' => $user,
            'client' => $client,
            'stats' => $stats,
            'recentRequests' => $recentRequests,
            'reviews' => $reviews
        ]);
    }

    /**
     * Supprime l'avatar du client.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAvatar()
    {
        $user = Auth::user();
        $client = $user->client;
        
        if ($client && $client->avatar) {
            // Supprimer le fichier du stockage
            if (Storage::disk('public')->exists($client->avatar)) {
                Storage::disk('public')->delete($client->avatar);
            }
            
            // Mettre à jour la base de données
            $client->avatar = null;
            $client->save();
        }
        
        return redirect()->route('client.profile.edit')
            ->with('success', 'Avatar supprimé avec succès.');
    }
}
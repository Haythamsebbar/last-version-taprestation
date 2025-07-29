<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Prestataire;
use App\Models\Skill;
use App\Models\Category;
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
        $this->middleware(['auth', 'role:prestataire']);
    }

    /**
     * Affiche le formulaire d'édition du profil.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        $skills = Skill::all();
        $categories = Category::all();
        
        // Calculer le pourcentage de complétion du profil
        $completionPercentage = $prestataire ? $this->calculateProfileCompletion($prestataire) : 0;
        
        return view('prestataire.profile.edit', [
            'user' => $user,
            'prestataire' => $prestataire,
            'skills' => $skills,
            'categories' => $categories,
            'completion_percentage' => $completionPercentage
        ]);
    }

    /**
     * Met à jour le profil du prestataire.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;

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
            'description' => 'nullable|string|min:200|max:2000',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
            'sector' => 'nullable|string|max:255',
            'portfolio' => 'nullable|url|max:500',
            'daily_rate' => 'nullable|numeric|min:0|max:9999.99',
            'average_delivery_time' => 'nullable|integer|min:1|max:365',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'portfolio_images' => 'nullable|array|max:10',
            'portfolio_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'portfolio_titles' => 'nullable|array',
            'portfolio_titles.*' => 'nullable|string|max:100',
            'portfolio_descriptions' => 'nullable|array',
            'portfolio_descriptions.*' => 'nullable|string|max:500',
            'portfolio_links' => 'nullable|array',
            'portfolio_links.*' => 'nullable|url|max:500',
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

        // Mise à jour du profil prestataire
        if (!$prestataire) {
            $prestataire = new Prestataire();
            $prestataire->user_id = $user->id;
            $prestataire->is_approved = false;
        }

        $prestataireData = $request->only(['phone', 'description', 'daily_rate', 'average_delivery_time']);
        $prestataireData['secteur_activite'] = $request->input('sector');

        // Gestion de la photo de profil
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo s'il existe
            if ($prestataire->photo && Storage::disk('public')->exists($prestataire->photo)) {
                Storage::disk('public')->delete($prestataire->photo);
            }

            // Stocker la nouvelle photo
            $prestataireData['photo'] = $request->file('photo')->store('photos/prestataires', 'public');
        }

        // Gestion du portfolio
        if ($request->hasFile('portfolio_images')) {
            $portfolioData = $prestataire->portfolio_images ?? [];

            foreach ($request->file('portfolio_images') as $index => $file) {
                $imagePath = $file->store('portfolio/prestataires', 'public');

                $portfolioData[] = [
                    'image' => $imagePath,
                    'title' => $request->portfolio_titles[$index] ?? '',
                    'description' => $request->portfolio_descriptions[$index] ?? '',
                    'link' => $request->portfolio_links[$index] ?? '',
                    'created_at' => now()->toISOString()
                ];
            }

            $prestataireData['portfolio_images'] = $portfolioData;
        }

        $prestataire->fill($prestataireData);
        $prestataire->save();

        // Mise à jour des compétences
        if ($request->has('skills')) {
            $prestataire->skills()->sync($request->skills);
        } else {
            $prestataire->skills()->detach();
        }

        // Calculer le pourcentage de complétion du profil
        $completionPercentage = $this->calculateProfileCompletion($prestataire);

        return redirect()->route('prestataire.profile.edit')
            ->with('success', 'Profil mis à jour avec succès !')
            ->with('completion_percentage', $completionPercentage);
    }

    /**
     * Affiche le profil public du prestataire.
     *
     * @param  \App\Models\Prestataire|null  $prestataire
     * @return \Illuminate\View\View
     */
    public function show(Prestataire $prestataire = null)
    {
        // Si aucun prestataire n'est fourni, utiliser celui de l'utilisateur connecté
        if (!$prestataire) {
            $user = Auth::user();
            $prestataire = $user->prestataire;
            
            if (!$prestataire) {
                return redirect()->route('prestataire.dashboard')
                    ->with('error', 'Profil prestataire non trouvé.');
            }
        }
        
        // Statistiques du prestataire
        $stats = [
            'total_services' => $prestataire->services()->count(),
            'active_services' => $prestataire->services()->where('status', 'active')->count(),
            'total_reviews' => $prestataire->reviews()->count(),
            'average_rating' => $prestataire->reviews()->avg('rating') ?: 0,
            'member_since' => $prestataire->user->created_at->format('F Y'),
            'approval_status' => $prestataire->is_approved ? 'approved' : 'pending'
        ];
        
        // Services récents
        $recentServices = $prestataire->services()
            ->where('status', 'active')
            ->latest()
            ->take(3)
            ->get();
        
        // Avis récents
        $recentReviews = $prestataire->reviews()
            ->with('client.user')
            ->latest()
            ->take(5)
            ->get();
        
        return view('prestataire.profile.show', [
            'user' => $prestataire->user,
            'prestataire' => $prestataire,
            'stats' => $stats,
            'recentServices' => $recentServices,
            'recentReviews' => $recentReviews
        ]);
    }

    /**
     * Supprime la photo du prestataire.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePhoto()
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        if ($prestataire && $prestataire->photo) {
            // Supprimer le fichier du stockage
            if (Storage::disk('public')->exists($prestataire->photo)) {
                Storage::disk('public')->delete($prestataire->photo);
            }
            
            // Mettre à jour la base de données
            $prestataire->photo = null;
            $prestataire->save();
        }
        
        // Calculer le pourcentage de complétion du profil
        $completionPercentage = $this->calculateProfileCompletion($prestataire);
        
        return redirect()->route('prestataire.profile.edit')
            ->with('success', 'Photo supprimée avec succès.')
            ->with('completion_percentage', $completionPercentage);
    }

    /**
     * Affiche le profil public d'un prestataire (accessible à tous).
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function publicShow($id)
    {
        $prestataire = Prestataire::with(['user', 'skills', 'services', 'reviews.client.user'])
            ->where('is_approved', true)
            ->findOrFail($id);
        
        // Statistiques publiques
        $stats = [
            'total_services' => $prestataire->services()->where('status', 'active')->count(),
            'total_reviews' => $prestataire->reviews()->count(),
            'average_rating' => round($prestataire->reviews()->avg('rating') ?: 0, 1),
            'member_since' => $prestataire->user->created_at->format('F Y')
        ];
        
        // Services actifs
        $services = $prestataire->services()
            ->where('status', 'active')
            ->latest()
            ->paginate(6);
        
        // Avis récents
        $reviews = $prestataire->reviews()
            ->with('client.user')
            ->latest()
            ->paginate(10);
        
        return view('prestataire.profile.public', [
            'prestataire' => $prestataire,
            'stats' => $stats,
            'services' => $services,
            'reviews' => $reviews
        ]);
    }
    
    /**
     * Calcule le pourcentage de complétion du profil.
     *
     * @param  \App\Models\Prestataire  $prestataire
     * @return int
     */
    private function calculateProfileCompletion($prestataire)
    {
        $fields = [
            'photo' => $prestataire->photo ? 15 : 0,
            'description' => ($prestataire->description && strlen($prestataire->description) >= 200) ? 20 : 0,
            'phone' => $prestataire->phone ? 10 : 0,
            'skills' => $prestataire->skills()->count() > 0 ? 15 : 0,
            'secteur_activite' => $prestataire->secteur_activite ? 10 : 0,
            // 'hourly_rate' => $prestataire->hourly_rate ? 10 : 0, // Supprimé pour confidentialité
            'portfolio' => (is_array($prestataire->portfolio_images) && count($prestataire->portfolio_images) > 0) ? 20 : 0,
        ];
        
        return array_sum($fields);
    }
    
    /**
     * Supprime un élément du portfolio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePortfolioItem(Request $request)
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        $index = $request->input('index');
        
        if ($prestataire && is_array($prestataire->portfolio_images) && isset($prestataire->portfolio_images[$index])) {
            $portfolioData = $prestataire->portfolio_images;
            
            // Supprimer le fichier image du stockage
            if (isset($portfolioData[$index]['image']) && Storage::disk('public')->exists($portfolioData[$index]['image'])) {
                Storage::disk('public')->delete($portfolioData[$index]['image']);
            }
            
            // Supprimer l'élément du tableau
            unset($portfolioData[$index]);
            $prestataire->portfolio_images = array_values($portfolioData); // Réindexer le tableau
            $prestataire->save();
        }
        
        return redirect()->route('prestataire.profile.edit')
            ->with('success', 'Élément du portfolio supprimé avec succès.');
    }
    
    /**
     * Affiche l'aperçu du profil public.
     *
     * @return \Illuminate\View\View
     */
    public function preview()
    {
        $user = Auth::user();
        $prestataire = $user->prestataire;
        
        if (!$prestataire) {
            return redirect()->route('prestataire.dashboard')
                ->with('error', 'Profil prestataire non trouvé.');
        }
        
        // Statistiques du prestataire
        $stats = [
            'total_services' => $prestataire->services()->where('status', 'active')->count(),
            'total_reviews' => $prestataire->reviews()->count(),
            'average_rating' => round($prestataire->reviews()->avg('rating') ?: 0, 1),
            'member_since' => $prestataire->user->created_at->format('F Y')
        ];
        
        // Services actifs
        $services = $prestataire->services()
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();
        
        // Avis récents
        $reviews = $prestataire->reviews()
            ->with('client.user')
            ->latest()
            ->take(5)
            ->get();
        
        return view('prestataire.profile.preview', [
            'prestataire' => $prestataire,
            'stats' => $stats,
            'services' => $services,
            'reviews' => $reviews,
            'is_preview' => true
        ]);
    }
}
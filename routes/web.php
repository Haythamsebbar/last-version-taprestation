<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PrestataireController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\SavedSearchController;
use App\Http\Controllers\MatchingAlertController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\UrgentSaleController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\EquipmentController;
use App\Models\Video;
use App\Http\Controllers\Prestataire\MissionController;
use App\Http\Controllers\Prestataire\VerificationController;
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::get('/prestataire/missions', [MissionController::class, 'index'])->name('prestataire.missions.index');
use App\Http\Controllers\Prestataire\ServiceImageController;
use App\Http\Controllers\Prestataire\AvailabilityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');



// API Routes
Route::get('/api/categories/{category}/subcategories', function($categoryId) {
    $subcategories = \App\Models\Category::where('parent_id', $categoryId)->get();
    return response()->json($subcategories);
});

// Route de géocodage simple
Route::get('/api/geocode', function(\Illuminate\Http\Request $request) {
    $address = $request->get('address');
    
    if (empty($address)) {
        return response()->json(['success' => false, 'message' => 'Adresse requise']);
    }
    
    // Géocodage simple basé sur les villes françaises connues
    $frenchCities = [
        'paris' => ['latitude' => 48.8566, 'longitude' => 2.3522],
        'marseille' => ['latitude' => 43.2965, 'longitude' => 5.3698],
        'lyon' => ['latitude' => 45.7640, 'longitude' => 4.8357],
        'toulouse' => ['latitude' => 43.6047, 'longitude' => 1.4442],
        'nice' => ['latitude' => 43.7102, 'longitude' => 7.2620],
        'nantes' => ['latitude' => 47.2184, 'longitude' => -1.5536],
        'montpellier' => ['latitude' => 43.6110, 'longitude' => 3.8767],
        'strasbourg' => ['latitude' => 48.5734, 'longitude' => 7.7521],
        'bordeaux' => ['latitude' => 44.8378, 'longitude' => -0.5792],
        'lille' => ['latitude' => 50.6292, 'longitude' => 3.0573],
        'rennes' => ['latitude' => 48.1173, 'longitude' => -1.6778],
        'reims' => ['latitude' => 49.2583, 'longitude' => 4.0317],
        'toulon' => ['latitude' => 43.1242, 'longitude' => 5.9280],
        'saint-etienne' => ['latitude' => 45.4397, 'longitude' => 4.3872],
        'le havre' => ['latitude' => 49.4944, 'longitude' => 0.1079],
        'grenoble' => ['latitude' => 45.1885, 'longitude' => 5.7245],
        'dijon' => ['latitude' => 47.3220, 'longitude' => 5.0415],
        'angers' => ['latitude' => 47.4784, 'longitude' => -0.5632],
        'nimes' => ['latitude' => 43.8367, 'longitude' => 4.3601],
        'villeurbanne' => ['latitude' => 45.7665, 'longitude' => 4.8795]
    ];
    
    $addressLower = strtolower($address);
    
    foreach ($frenchCities as $city => $coords) {
        if (strpos($addressLower, $city) !== false) {
            return response()->json([
                'success' => true,
                'latitude' => $coords['latitude'],
                'longitude' => $coords['longitude'],
                'city' => ucfirst($city)
            ]);
        }
    }
    
    // Si aucune ville trouvée, retourner une erreur
     return response()->json(['success' => false, 'message' => 'Ville non trouvée']);
 });

// API de géocodage inverse (coordonnées vers adresse)
Route::get('/api/reverse-geocode', [App\Http\Controllers\GeocodingController::class, 'reverseGeocode']);

// Verification Routes
Route::middleware(['auth', 'role:prestataire'])->group(function () {
    Route::post('/verification-requests', [\App\Http\Controllers\VerificationController::class, 'store'])->name('verification.store');
});

Route::middleware(['auth', 'role:administrateur'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Routes pour la gestion des vérifications
    Route::prefix('verifications')->name('verifications.')->group(function () {
        Route::get('/', [AdminVerificationController::class, 'index'])->name('index');
        Route::get('/{verificationRequest}', [AdminVerificationController::class, 'show'])->name('show');
        Route::patch('/{verificationRequest}/approve', [AdminVerificationController::class, 'approve'])->name('approve');
        Route::patch('/{verificationRequest}/reject', [AdminVerificationController::class, 'reject'])->name('reject');
        Route::get('/{verificationRequest}/document/{documentIndex}', [AdminVerificationController::class, 'downloadDocument'])->name('download-document');
        Route::post('/run-automatic', [AdminVerificationController::class, 'runAutomaticVerification'])->name('run-automatic');
        Route::patch('/{prestataire}/revoke', [AdminVerificationController::class, 'revokeVerification'])->name('revoke');
    });
});

// Pages statiques
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_started' => session()->isStarted(),
        'session_token' => session()->token()
    ]);
});

// Debug route for CSRF testing
Route::get('/debug-csrf', function () {
    session()->start();
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_token' => session()->token(),
        'session_id' => session()->getId(),
        'session_started' => session()->isStarted(),
        'app_key_set' => !empty(config('app.key'))
    ]);
});

// Test POST route to check CSRF
Route::post('/test-csrf', function (Illuminate\Http\Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'CSRF token is working!',
        'token_received' => $request->input('_token'),
        'session_token' => session()->token()
    ]);
});

// Simple test form to verify CSRF
Route::get('/test-form', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>CSRF Test</title>
        <meta name="csrf-token" content="' . csrf_token() . '">
    </head>
    <body>
        <h1>CSRF Test Form</h1>
        <form method="POST" action="/test-csrf">
            ' . csrf_field() . '
            <input type="text" name="test_field" value="test_value" required>
            <button type="submit">Test Submit</button>
        </form>
        
        <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch("/test-csrf", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").getAttribute("content")
                }
            })
            .then(response => response.json())
            .then(data => {
                alert("Success: " + JSON.stringify(data));
            })
            .catch(error => {
                alert("Error: " + error);
            });
        });
        </script>
    </body>
    </html>';
});

// Password Reset Routes
Route::get('/password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

// Routes pour les catégories
Route::get('/categories/{category}/subcategories', [\App\Http\Controllers\CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
Route::get('/categories/main', [\App\Http\Controllers\CategoryController::class, 'getMainCategories'])->name('categories.main');
Route::get('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'getCategory'])->name('categories.show');
Route::post('/password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');



// Routes pour les services
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

// Routes pour les prestataires
Route::get('/prestataires', [PrestataireController::class, 'index'])->name('prestataires.index');
Route::get('/prestataires/{prestataire}', [PrestataireController::class, 'show'])->name('prestataires.show');

// Routes pour les équipements
Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
Route::get('/equipment/{equipment}/reserve', [EquipmentController::class, 'reserve'])->name('equipment.reserve');
Route::post('/equipment/{equipment}/rent', [EquipmentController::class, 'rent'])->name('equipment.rent');


// Routes de recherche
Route::get('/search', [SearchController::class, 'searchPrestataires'])->name('search.index');
Route::post('/search', [SearchController::class, 'searchPrestataires'])->name('search.results');
Route::get('/search/prestataires', [SearchController::class, 'searchPrestataires'])->name('search.prestataires');
Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');

// Routes pour les articles
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

// Routes pour les vidéos
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/videos/feed', [VideoController::class, 'index'])->name('videos.feed');

Route::get('/approve-all-videos', function () {
    $updatedCount = App\Models\Video::where('status', 'pending')->update(['status' => 'approved']);
    return $updatedCount . ' videos have been approved.';
});
Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');
Route::post('/videos/{video}/like', [VideoController::class, 'like'])->name('videos.like');
Route::post('/videos/{video}/comments', [VideoController::class, 'comment'])->name('videos.comment');
Route::get('/videos/{video}/comments', [VideoController::class, 'getComments'])->name('videos.comments.get');
Route::post('/prestataires/{prestataire}/follow', [VideoController::class, 'follow'])->name('prestataires.follow');

// Routes pour les avis
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::get('/reviews/with-photos', [ReviewController::class, 'withPhotos'])->name('reviews.with-photos');
Route::get('/reviews/certificates', [ReviewController::class, 'certificates'])->name('reviews.certificates');

// Routes protégées (authentification requise)
Route::middleware(['auth'])->group(function () {

    // Booking management routes
    Route::post('/bookings/{booking}/refuse', [BookingController::class, 'refuse'])->name('bookings.refuse');
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::put('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');


    Route::get('/api/prestataire/agenda/events', [App\Http\Controllers\Prestataire\AgendaController::class, 'events'])->name('api.prestataire.agenda.events');
    Route::get('/api/prestataire/agenda/recent-bookings', [App\Http\Controllers\Prestataire\AgendaController::class, 'recentBookings'])->name('api.prestataire.agenda.recent-bookings');
    
    // Dashboard - Redirection selon le rôle
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('client')) {
            return redirect()->route('client.dashboard');
        } elseif ($user->hasRole('prestataire')) {
            return redirect()->route('prestataire.dashboard');
        } elseif ($user->hasRole('administrateur')) {
            return redirect()->route('admin.dashboard');
        }
        // Fallback vers la page d'accueil si aucun rôle reconnu
        return redirect()->route('home');
    })->name('dashboard');
    
    // Route générale pour l'édition de profil (redirige selon le rôle)
    Route::get('/profile/edit', function () {
        $user = auth()->user();
        if ($user->hasRole('client')) {
            return redirect()->route('client.profile.edit');
        } elseif ($user->hasRole('prestataire')) {
            return redirect()->route('prestataire.profile.edit');
        }
        return redirect()->route('dashboard');
    })->name('profile.edit');
    
    // Route générale pour les paramètres de profil (redirige selon le rôle)
    Route::get('/profile/settings', function () {
        $user = auth()->user();
        if ($user->hasRole('client')) {
            return redirect()->route('client.profile.edit');
        } elseif ($user->hasRole('prestataire')) {
            return redirect()->route('prestataire.profile.edit');
        }
        return redirect()->route('profile.edit');
    })->name('profile.settings');
    
    // Routes pour les clients
    Route::middleware(['role:client'])->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/delete-avatar', [\App\Http\Controllers\Client\ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');
        Route::get('/bookings', [BookingController::class, 'clientBookings'])->name('bookings.index');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::get('/favorites', [ClientController::class, 'favorites'])->name('favorites');
        Route::post('/favorites/{prestataire}', [ClientController::class, 'toggleFavorite'])->name('favorites.toggle');
        Route::get('/follows', [ClientController::class, 'follows'])->name('follows.index');
        
        // Routes pour suivre/ne plus suivre les prestataires
        Route::post('/prestataire-follows/{prestataire}/follow', [\App\Http\Controllers\Client\PrestataireFollowController::class, 'follow'])->name('prestataire-follows.follow');
        Route::delete('/prestataire-follows/{prestataire}/unfollow', [\App\Http\Controllers\Client\PrestataireFollowController::class, 'unfollow'])->name('prestataire-follows.unfollow');
        Route::get('/prestataire-follows', [\App\Http\Controllers\Client\PrestataireFollowController::class, 'index'])->name('prestataire-follows.index');
        
        // Routes pour la messagerie client
        Route::get('messaging', [\App\Http\Controllers\Client\MessagingController::class, 'index'])->name('messaging.index');
        Route::get('messaging/{user}', [\App\Http\Controllers\Client\MessagingController::class, 'show'])->name('messaging.show');
        Route::post('messaging/{user}', [\App\Http\Controllers\Client\MessagingController::class, 'store'])->name('messaging.store');
        Route::get('messaging/start/{prestataire}', [\App\Http\Controllers\Client\MessagingController::class, 'startConversation'])->name('messaging.start');
        
        // Routes pour la navigation des prestataires
        Route::get('browse/prestataires', [\App\Http\Controllers\Client\PrestataireController::class, 'index'])->name('browse.prestataires');
        Route::get('browse/prestataire/{prestataire}', [\App\Http\Controllers\Client\PrestataireController::class, 'show'])->name('browse.prestataire');
        Route::get('browse/prestataires/{prestataire}', [\App\Http\Controllers\Client\PrestataireController::class, 'show'])->name('browse.prestataires.show');
        Route::get('prestataires', [\App\Http\Controllers\Client\PrestataireController::class, 'index'])->name('prestataires.index');
        Route::get('prestataires/{prestataire}', [\App\Http\Controllers\Client\PrestataireController::class, 'show'])->name('prestataires.show');
        
        // Routes pour les évaluations des missions
        
        
        // Routes pour les offres
        Route::get('/offers', [\App\Http\Controllers\Client\OfferController::class, 'index'])->name('offers.index');
        Route::post('/offers/{offer}/accept', [\App\Http\Controllers\Client\OfferController::class, 'accept'])->name('offers.accept');
        Route::post('/offers/{offer}/reject', [\App\Http\Controllers\Client\OfferController::class, 'reject'])->name('offers.reject');
        
        // Route alternative pour la messagerie (maintenant gérée ci-dessus)
        
        // Routes pour les actualités client
        Route::get('/news', [\App\Http\Controllers\Client\NewsController::class, 'index'])->name('news.index');
        
        // Route pour afficher toutes les demandes unifiées (doit être avant la route resource)
        Route::get('/requests/all', [\App\Http\Controllers\Client\DashboardController::class, 'allRequests'])->name('requests.all');
        
        // Routes pour les demandes de service
        Route::resource('requests', \App\Http\Controllers\Client\ClientRequestController::class);

        // Routes pour l'aide client
        Route::get('/help', [\App\Http\Controllers\Client\HelpController::class, 'index'])->name('help.index');
    });

    // Routes for Prestataires
    Route::middleware(['role:prestataire'])->prefix('prestataire')->name('prestataire.')->group(function () {
        Route::get('videos-manage', [App\Http\Controllers\Prestataire\VideoController::class, 'manage'])->name('videos.manage');
    });
    
    // Routes publiques pour la location de matériel
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EquipmentController::class, 'index'])->name('index');
        Route::get('/{equipment}', [\App\Http\Controllers\EquipmentController::class, 'show'])->name('show');
        Route::get('/{equipment}/reserve', [\App\Http\Controllers\EquipmentController::class, 'showReservationForm'])->name('reserve');
Route::post('/{equipment}/rent', [\App\Http\Controllers\EquipmentController::class, 'rent'])->name('rent');
        Route::post('/{equipment}/report', [\App\Http\Controllers\EquipmentController::class, 'submitReport'])->name('report');
    });
    
    // Routes publiques pour les ventes urgentes
    Route::prefix('urgent-sales')->name('urgent-sales.')->group(function () {
        Route::get('/', [\App\Http\Controllers\UrgentSaleController::class, 'index'])->name('index');
        Route::get('/{urgentSale}', [\App\Http\Controllers\UrgentSaleController::class, 'show'])->name('show');
        Route::post('/{urgentSale}/contact', [\App\Http\Controllers\UrgentSaleController::class, 'contact'])->name('contact');
        Route::post('/{urgentSale}/report', [\App\Http\Controllers\UrgentSaleController::class, 'report'])->name('report');
    });
    
    // Routes pour les clients - location de matériel
    Route::middleware(['role:client'])->prefix('client')->name('client.')->group(function () {
        // Routes pour les demandes de location
        Route::prefix('equipment-rental-requests')->name('equipment-rental-requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Client\EquipmentRentalRequestController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Client\EquipmentRentalRequestController::class, 'store'])->name('store');
            Route::get('/{request}', [\App\Http\Controllers\Client\EquipmentRentalRequestController::class, 'show'])->name('show');
            Route::delete('/{request}', [\App\Http\Controllers\Client\EquipmentRentalRequestController::class, 'destroy'])->name('destroy');
        });
        
        // Routes pour les locations actives
        Route::prefix('equipment-rentals')->name('equipment-rentals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Client\EquipmentRentalController::class, 'index'])->name('index');
            Route::get('/{rental}', [\App\Http\Controllers\Client\EquipmentRentalController::class, 'show'])->name('show');
            Route::post('/{rental}/confirm-receipt', [\App\Http\Controllers\Client\EquipmentRentalController::class, 'confirmReceipt'])->name('confirm-receipt');
            Route::post('/{rental}/confirm-return', [\App\Http\Controllers\Client\EquipmentRentalController::class, 'confirmReturn'])->name('confirm-return');
            Route::post('/{rental}/review', [\App\Http\Controllers\Client\EquipmentRentalController::class, 'review'])->name('review');
        });
    });

    // Route générale pour la messagerie (accessible à tous les utilisateurs authentifiés)
    Route::middleware(['auth'])->get('/messaging', function () {
        $user = auth()->user();
        if ($user->hasRole('client')) {
            return redirect()->route('client.messages.index');
        } elseif ($user->hasRole('prestataire')) {
            return redirect()->route('prestataire.messaging.index');
        }
        return redirect()->route('dashboard');
    })->name('messaging.index');

    // Routes pour les notifications (accessible à tous les utilisateurs authentifiés)
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::post('/{notification}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-as-read');
    Route::post('/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    // Routes AJAX
    Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    Route::get('/recent', [\App\Http\Controllers\NotificationController::class, 'getRecent'])->name('recent');
});
    
    // Route pour les prestataires en attente d'approbation
    Route::middleware(['auth', 'role:prestataire'])->get('/prestataire/pending-approval', function () {
        return view('prestataire.pending_approval');
    })->name('prestataire.pending-approval');
    
    // Routes pour les prestataires
    Route::middleware(['role:prestataire'])->prefix('prestataire')->name('prestataire.')->group(function () {
        Route::put('availability/update-weekly', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'updateWeeklyAvailability'])->name('availability.updateWeekly');
        Route::resource('bookings', App\Http\Controllers\Prestataire\BookingController::class)->only(['index', 'show']);
        Route::patch('/bookings/{booking}/accept', [\App\Http\Controllers\Prestataire\BookingController::class, 'accept'])->name('bookings.accept');
        Route::patch('/bookings/{booking}/reject', [\App\Http\Controllers\Prestataire\BookingController::class, 'reject'])->name('bookings.reject');
        Route::resource('agenda', App\Http\Controllers\Prestataire\AgendaController::class)->only(['index']);
        Route::get('/dashboard', [\App\Http\Controllers\Prestataire\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Prestataire\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Prestataire\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/photo', [\App\Http\Controllers\Prestataire\ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
        Route::delete('/profile/portfolio/{index}', [\App\Http\Controllers\Prestataire\ProfileController::class, 'deletePortfolioItem'])->name('profile.delete-portfolio-item');
        Route::get('/profile/preview', [\App\Http\Controllers\Prestataire\ProfileController::class, 'preview'])->name('profile.preview');
        Route::get('/profile/public/{id}', [\App\Http\Controllers\Prestataire\ProfileController::class, 'publicShow'])->name('profile.public');
        Route::get('/profile/{prestataire}', [\App\Http\Controllers\Prestataire\ProfileController::class, 'show'])->name('profile');
        
        // Routes pour la vérification
        Route::prefix('verification')->name('verification.')->group(function () {
            Route::get('/', [VerificationController::class, 'index'])->name('index');
            Route::get('/create', [VerificationController::class, 'create'])->name('create');
            Route::post('/', [VerificationController::class, 'store'])->name('store');
            Route::get('/{verificationRequest}', [VerificationController::class, 'show'])->name('show');
            Route::get('/{verificationRequest}/document/{documentIndex}', [VerificationController::class, 'downloadDocument'])->name('download-document');
            Route::post('/check-automatic', [VerificationController::class, 'checkAutomaticCriteria'])->name('check-automatic');
        });
        
        

        
        // Routes pour les réponses/offres envoyées
        Route::get('/responses', [\App\Http\Controllers\Prestataire\ResponseController::class, 'index'])->name('responses.index');
        Route::get('/responses/{offer}', [\App\Http\Controllers\Prestataire\ResponseController::class, 'show'])->name('responses.show');
        Route::put('/responses/{offer}', [\App\Http\Controllers\Prestataire\ResponseController::class, 'update'])->name('responses.update');
        Route::delete('/responses/{offer}', [\App\Http\Controllers\Prestataire\ResponseController::class, 'cancel'])->name('responses.cancel');
        
        // Routes pour les missions
        // Route pour la confirmation de la fin de mission par le prestataire
        Route::post('/missions/{request}/confirm', [MissionController::class, 'confirmCompletion'])->name('missions.confirm');
        
        Route::resource('services', \App\Http\Controllers\Prestataire\ServiceController::class);
        Route::get('services/{service}/availabilities', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'index'])->name('availabilities.index');
        Route::post('services/{service}/availabilities', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'store'])->name('availabilities.store');
        Route::delete('availabilities/{availability}', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'destroy'])->name('availabilities.destroy');
          Route::delete('/services/images/{image}', [ServiceImageController::class, 'destroy'])->name('services.images.destroy');
        Route::get('/bookings', [BookingController::class, 'prestataireBookings'])->name('bookings.index');
        // Routes pour la gestion des disponibilités
        Route::prefix('availability')->name('availability.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'index'])->name('index');
            Route::get('/calendar', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'calendar'])->name('calendar');
            Route::get('/events', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'events'])->name('events');
                Route::post('/add-exception', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'addException'])->name('add-exception');
            Route::delete('/exception/{id}', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'deleteException'])->name('delete-exception');
            Route::post('/update-settings', [\App\Http\Controllers\Prestataire\AvailabilityController::class, 'updateBookingSettings'])->name('update-settings');
        });
        
        Route::get('/calendar', [PrestataireController::class, 'calendar'])->name('calendar');
        Route::get('/visibility', [PrestataireController::class, 'visibility'])->name('visibility');
        
        // Routes pour la messagerie prestataire
        Route::get('/messaging', [\App\Http\Controllers\Prestataire\MessagingController::class, 'index'])->name('messaging.index');
        
        // Routes pour l'agenda prestataire
        Route::get('/agenda', [\App\Http\Controllers\Prestataire\AgendaController::class, 'index'])->name('agenda.index');
        Route::get('/agenda/events', [\App\Http\Controllers\Prestataire\AgendaController::class, 'events'])->name('agenda.events');
        Route::get('/agenda/booking/{booking}', [\App\Http\Controllers\Prestataire\AgendaController::class, 'show'])->name('agenda.booking.show');
        Route::put('/agenda/booking/{booking}/status', [\App\Http\Controllers\Prestataire\AgendaController::class, 'updateStatus'])->name('agenda.booking.update-status');
        
        // Routes pour la messagerie prestataire
        Route::get('/messages', [\App\Http\Controllers\Prestataire\MessagingController::class, 'index'])->name('messages.index');
        Route::get('/messages/{conversation}', [\App\Http\Controllers\Prestataire\MessagingController::class, 'show'])->name('messages.show');
        Route::post('/messages', [\App\Http\Controllers\Prestataire\MessagingController::class, 'store'])->name('messages.store');
        Route::post('/messages/start-conversation', [\App\Http\Controllers\Prestataire\MessagingController::class, 'startConversation'])->name('messages.start-conversation');
        
        // Routes pour la gestion des équipements
        Route::prefix('equipment')->name('equipment.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Prestataire\EquipmentController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Prestataire\EquipmentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Prestataire\EquipmentController::class, 'store'])->name('store');
            Route::get('/{equipment}', [\App\Http\Controllers\Prestataire\EquipmentController::class, 'show'])->name('show');
            Route::get('/{equipment}/edit', [\App\Http\Controllers\Prestataire\EquipmentController::class, 'edit'])->name('edit');
            Route::put('/{equipment}', [\App\Http\Controllers\Prestataire\EquipmentController::class, 'update'])->name('update');
            Route::delete('/{equipment}', [\App\Http\Controllers\Prestataire\EquipmentController::class, 'destroy'])->name('destroy');
            Route::post('/{equipment}/toggle-status', [\App\Http\Controllers\Prestataire\EquipmentController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // Routes pour les demandes de location d'équipement
        Route::prefix('equipment-rental-requests')->name('equipment-rental-requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Prestataire\EquipmentRentalRequestController::class, 'index'])->name('index');
            Route::get('/{request}', [\App\Http\Controllers\Prestataire\EquipmentRentalRequestController::class, 'show'])->name('show');
            Route::patch('/{request}/accept', [\App\Http\Controllers\Prestataire\EquipmentRentalRequestController::class, 'accept'])->name('accept');
            Route::patch('/{request}/reject', [\App\Http\Controllers\Prestataire\EquipmentRentalRequestController::class, 'reject'])->name('reject');
        });
        
        // Routes pour les locations d'équipement
        Route::prefix('equipment-rentals')->name('equipment-rentals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Prestataire\EquipmentRentalController::class, 'index'])->name('index');
            Route::get('/{rental}', [\App\Http\Controllers\Prestataire\EquipmentRentalController::class, 'show'])->name('show');
            Route::post('/{rental}/start', [\App\Http\Controllers\Prestataire\EquipmentRentalController::class, 'start'])->name('start');
            Route::post('/{rental}/complete', [\App\Http\Controllers\Prestataire\EquipmentRentalController::class, 'complete'])->name('complete');
            Route::post('/{rental}/report-issue', [\App\Http\Controllers\Prestataire\EquipmentRentalController::class, 'reportIssue'])->name('report-issue');
        });
        
        // Routes pour les ventes urgentes (prestataire)
        Route::prefix('urgent-sales')->name('urgent-sales.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'store'])->name('store');
            Route::get('/{urgentSale}', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'show'])->name('show');
            Route::get('/{urgentSale}/edit', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'edit'])->name('edit');
            Route::put('/{urgentSale}', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'update'])->name('update');
            Route::delete('/{urgentSale}', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'destroy'])->name('destroy');
            Route::post('/{urgentSale}/update-status', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'updateStatus'])->name('update-status');
            Route::get('/{urgentSale}/contacts', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'contacts'])->name('contacts');
            Route::post('/contacts/{contact}/respond', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'respondToContact'])->name('contacts.respond');
            Route::patch('/contacts/{contact}/accept', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'acceptContact'])->name('contacts.accept');
            Route::patch('/contacts/{contact}/reject', [\App\Http\Controllers\Prestataire\UrgentSaleController::class, 'rejectContact'])->name('contacts.reject');
        });

        
        Route::prefix('videos')->name('videos.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Prestataire\VideoController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Prestataire\VideoController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Prestataire\VideoController::class, 'store'])->name('store');
            Route::get('/{video}/edit', [\App\Http\Controllers\Prestataire\VideoController::class, 'edit'])->name('edit');
            Route::put('/{video}', [\App\Http\Controllers\Prestataire\VideoController::class, 'update'])->name('update');
            Route::delete('/{video}', [\App\Http\Controllers\Prestataire\VideoController::class, 'destroy'])->name('destroy');
        });

        // Routes pour l'aide prestataire
        Route::get('/help', [\App\Http\Controllers\Prestataire\HelpController::class, 'index'])->name('help.index');

        // Route pour le QR Code
        Route::get('/qrcode', [QrCodeController::class, 'show'])->name('qrcode.show');

    });
    
    // Routes pour les réservations
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create/{service}', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::put('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/{booking}/complete', [BookingController::class, 'complete'])->name('complete');
    });
    
    // Routes pour la messagerie
    Route::prefix('messaging')->name('messaging.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/start/{user}', [MessageController::class, 'start'])->name('start');
        Route::get('/conversation/{user}', [MessageController::class, 'conversation'])->name('conversation');
        Route::post('/send/{receiver}', [MessageController::class, 'send'])->name('send');
        Route::post('/send-ajax', [MessageController::class, 'sendAjax'])->name('send.ajax');
        Route::get('/new-messages/{user}', [MessageController::class, 'getNewMessages'])->name('new-messages');
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/user-status/{user}', [MessageController::class, 'getUserOnlineStatus'])->name('user-status');
        Route::post('/mark-as-read', [MessageController::class, 'markAsRead'])->name('mark-as-read');
    });
    

    
    // Routes pour les avis
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/', [ReviewController::class, 'store'])->name('store');
        Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('edit');
        Route::put('/{review}', [ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });
    
    // Routes pour les recherches sauvegardées
    Route::prefix('saved-searches')->name('saved-searches.')->group(function () {
        Route::get('/', [SavedSearchController::class, 'index'])->name('index');
        Route::post('/', [SavedSearchController::class, 'store'])->name('store');
        Route::delete('/{savedSearch}', [SavedSearchController::class, 'destroy'])->name('destroy');
    });
    
    // Routes pour les alertes
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', [MatchingAlertController::class, 'index'])->name('index');
        Route::post('/', [MatchingAlertController::class, 'store'])->name('store');
        Route::put('/{alert}', [MatchingAlertController::class, 'update'])->name('update');
        Route::delete('/{alert}', [MatchingAlertController::class, 'destroy'])->name('destroy');
        Route::put('/{alert}/mark-read', [MatchingAlertController::class, 'markAsRead'])->name('mark-read');
    });
    
    // Routes pour les administrateurs
    Route::middleware(['role:administrateur'])->prefix('administrateur')->name('administrateur.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart', [\App\Http\Controllers\Admin\DashboardController::class, 'getChartData'])->name('dashboard.chart');
        
        // Gestion des utilisateurs
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('/users/{user}/toggle-block', [\App\Http\Controllers\Admin\UserController::class, 'toggleBlock'])->name('users.toggle-block');
        Route::post('/users/bulk-unblock', [\App\Http\Controllers\Admin\UserController::class, 'bulkUnblock'])->name('users.bulk-unblock');
        Route::post('/users/bulk-block', [\App\Http\Controllers\Admin\UserController::class, 'bulkBlock'])->name('users.bulk-block');
        Route::post('/users/bulk-delete', [\App\Http\Controllers\Admin\UserController::class, 'bulkDelete'])->name('users.bulk-delete');
        Route::get('/users/export', [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
        
        // Gestion des prestataires
        Route::resource('prestataires', \App\Http\Controllers\Admin\PrestataireController::class);
        Route::get('/prestataires-pending', [\App\Http\Controllers\Admin\PrestataireController::class, 'pending'])->name('prestataires.pending');
        Route::post('/prestataires/{prestataire}/approve', [\App\Http\Controllers\Admin\PrestataireController::class, 'approve'])->name('prestataires.approve');
        Route::post('/prestataires/{prestataire}/revoke', [\App\Http\Controllers\Admin\PrestataireController::class, 'revoke'])->name('prestataires.revoke');
        
        Route::post('/prestataires/{prestataire}/toggle-block', [\App\Http\Controllers\Admin\PrestataireController::class, 'toggleBlock'])->name('prestataires.toggle-block');
        Route::post('/prestataires/bulk-unblock', [\App\Http\Controllers\Admin\PrestataireController::class, 'bulkUnblock'])->name('prestataires.bulk-unblock');
        Route::post('/prestataires/bulk-block', [\App\Http\Controllers\Admin\PrestataireController::class, 'bulkBlock'])->name('prestataires.bulk-block');
        Route::post('/prestataires/bulk-delete', [\App\Http\Controllers\Admin\PrestataireController::class, 'bulkDelete'])->name('prestataires.bulk-delete');
        Route::get('/prestataires/export', [\App\Http\Controllers\Admin\PrestataireController::class, 'export'])->name('prestataires.export');
        
        // Gestion des clients
        Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class);
        Route::post('/clients/{client}/toggle-block', [\App\Http\Controllers\Admin\ClientController::class, 'toggleBlock'])->name('clients.toggle-block');
        Route::post('/clients/bulk-unblock', [\App\Http\Controllers\Admin\ClientController::class, 'bulkUnblock'])->name('clients.bulk-unblock');
        Route::post('/clients/bulk-block', [\App\Http\Controllers\Admin\ClientController::class, 'bulkBlock'])->name('clients.bulk-block');
        Route::post('/clients/bulk-delete', [\App\Http\Controllers\Admin\ClientController::class, 'bulkDelete'])->name('clients.bulk-delete');
        Route::get('/clients/export', [\App\Http\Controllers\Admin\ClientController::class, 'export'])->name('clients.export');
        
        // Gestion des catégories
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        
        // Gestion des compétences
        Route::resource('skills', \App\Http\Controllers\Admin\SkillController::class);
        
        // Modération des services
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
        Route::post('/services/{service}/toggle-visibility', [\App\Http\Controllers\Admin\ServiceController::class, 'toggleVisibility'])->name('services.toggleVisibility');
        
        // Modération des avis
        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class);
        Route::post('/reviews/{review}/moderate', [\App\Http\Controllers\Admin\ReviewController::class, 'moderate'])->name('reviews.moderate');
        
        // Gestion des réservations
        Route::resource('bookings', \App\Http\Controllers\Admin\BookingController::class);
        Route::post('/bookings/{booking}/update-status', [\App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.update-status');
        Route::get('/bookings/export', [\App\Http\Controllers\Admin\BookingController::class, 'export'])->name('bookings.export');
        

        // Gestion des offres
        Route::resource('offers', \App\Http\Controllers\Admin\OfferController::class);
        Route::post('/offers/{offer}/update-status', [\App\Http\Controllers\Admin\OfferController::class, 'updateStatus'])->name('offers.update-status');
        Route::post('/offers/{offer}/moderate', [\App\Http\Controllers\Admin\OfferController::class, 'moderate'])->name('offers.moderate');
        Route::get('/offers/export', [\App\Http\Controllers\Admin\OfferController::class, 'export'])->name('offers.export');
        Route::get('/offers/analytics', [\App\Http\Controllers\Admin\OfferController::class, 'analytics'])->name('offers.analytics');
        
        // Gestion des notifications
        Route::resource('notifications', \App\Http\Controllers\Admin\NotificationController::class);
        Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::post('/notifications/send-custom', [\App\Http\Controllers\Admin\NotificationController::class, 'sendCustom'])->name('notifications.send-custom');
        Route::delete('/notifications/cleanup', [\App\Http\Controllers\Admin\NotificationController::class, 'cleanup'])->name('notifications.cleanup');
        Route::get('/notifications/analytics', [\App\Http\Controllers\Admin\NotificationController::class, 'analytics'])->name('notifications.analytics');
        
        // Gestion des messages
        Route::resource('messages', \App\Http\Controllers\Admin\MessageController::class);
        Route::post('/messages/{message}/moderate', [\App\Http\Controllers\Admin\MessageController::class, 'moderate'])->name('messages.moderate');
        Route::post('/messages/{message}/toggle-read', [\App\Http\Controllers\Admin\MessageController::class, 'toggleRead'])->name('messages.toggle-read');
        Route::post('/messages/bulk-delete', [\App\Http\Controllers\Admin\MessageController::class, 'bulkDelete'])->name('messages.bulk-delete');
        Route::get('/messages/export', [\App\Http\Controllers\Admin\MessageController::class, 'export'])->name('messages.export');
        Route::get('/messages/analytics', [\App\Http\Controllers\Admin\MessageController::class, 'analytics'])->name('messages.analytics');
        Route::post('/messages/cleanup', [\App\Http\Controllers\Admin\MessageController::class, 'cleanup'])->name('messages.cleanup');
        
        // Gestion des signalements
        Route::prefix('reports')->name('reports.')->group(function () {
            // Signalements des ventes urgentes
            Route::prefix('urgent-sales')->name('urgent-sales.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\UrgentSaleReportController::class, 'index'])->name('index');
                Route::get('/{report}', [\App\Http\Controllers\Admin\UrgentSaleReportController::class, 'show'])->name('show');
                Route::post('/{report}/update-status', [\App\Http\Controllers\Admin\UrgentSaleReportController::class, 'updateStatus'])->name('update-status');
                Route::delete('/{report}', [\App\Http\Controllers\Admin\UrgentSaleReportController::class, 'destroy'])->name('destroy');
            });
            
            // Signalements des équipements
            Route::prefix('equipments')->name('equipments.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\EquipmentReportController::class, 'index'])->name('index');
                Route::get('/{report}', [\App\Http\Controllers\Admin\EquipmentReportController::class, 'show'])->name('show');
                Route::post('/{report}/update-status', [\App\Http\Controllers\Admin\EquipmentReportController::class, 'updateStatus'])->name('update-status');
                Route::delete('/{report}', [\App\Http\Controllers\Admin\EquipmentReportController::class, 'destroy'])->name('destroy');
            });
            
            // Tous les signalements
            Route::prefix('all')->name('all.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\AllReportsController::class, 'index'])->name('index');
            });
        });
        
        // Rapports et analyses
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
            Route::get('/dashboard', [\App\Http\Controllers\Admin\ReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/users', [\App\Http\Controllers\Admin\ReportController::class, 'users'])->name('users');
            Route::get('/services', [\App\Http\Controllers\Admin\ReportController::class, 'services'])->name('services');
            Route::get('/bookings', [\App\Http\Controllers\Admin\ReportController::class, 'bookings'])->name('bookings');
            Route::get('/financial', [\App\Http\Controllers\Admin\ReportController::class, 'financial'])->name('financial');
            Route::get('/export/{type}', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
        });
        
        // Gestion des articles
        Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class);
        Route::post('/articles/{article}/publish', [\App\Http\Controllers\Admin\ArticleController::class, 'publish'])->name('articles.publish');
        Route::post('/articles/{article}/archive', [\App\Http\Controllers\Admin\ArticleController::class, 'archive'])->name('articles.archive');
        
        // Gestion des équipements
        Route::resource('equipment', \App\Http\Controllers\Admin\EquipmentController::class);
        
        // Gestion des commandes
        // Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class); // Temporairement commenté
        
        // Gestion des demandes clients
        Route::resource('client-requests', \App\Http\Controllers\Admin\ClientRequestController::class);
        Route::post('/client-requests/{clientRequest}/update-status', [\App\Http\Controllers\Admin\ClientRequestController::class, 'updateStatus'])->name('client-requests.update-status');
        Route::get('/client-requests/export', [\App\Http\Controllers\Admin\ClientRequestController::class, 'export'])->name('client-requests.export');
    });
});

// Add these routes in the admin section
// ... existing code ...
// Remove these duplicate lines (587-589):

// Route::get('/prestataires/{prestataire}/download-document/{type}', [\App\Http\Controllers\Admin\PrestataireController::class, 'downloadDocument'])->name('prestataires.download-document');
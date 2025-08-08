<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Offer;
use App\Models\Service;
use App\Models\ClientRequest;
use App\Notifications\NewMessageNotification;
use App\Notifications\NewClientRequestNotification;
use App\Notifications\AnnouncementStatusNotification;
use App\Notifications\OfferAcceptedNotification;
use App\Notifications\NewOfferNotification;
use App\Notifications\NewReviewNotification;
use App\Notifications\PrestataireApprovedNotification;
use Carbon\Carbon;

class NotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupération des utilisateurs
        $clients = User::where('role', 'client')->get();
        $prestataires = User::where('role', 'prestataire')->get();
        $admin = User::where('role', 'administrateur')->first();
        
        // Récupération des offres pour les notifications
        $offers = Offer::all();
        
        if ($offers->isEmpty()) {
            $this->command->info('Aucune offre trouvée. Certaines notifications ne seront pas créées.');
        }
        
        // Notifications pour les clients
        foreach ($clients as $index => $client) {
            // Notification de bienvenue
            Notification::create([
                'type' => 'welcome',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $client->id,
                'data' => json_encode([
                    'title' => 'Bienvenue sur TaPrestation',
                    'message' => 'Merci de vous être inscrit sur notre plateforme. Commencez à explorer les services disponibles !',
                ]),
                'read_at' => $index === 0 ? null : Carbon::now()->subDays(rand(1, 5)),
                'created_at' => Carbon::now()->subDays(rand(5, 10)),
                'updated_at' => Carbon::now()->subDays(rand(1, 5)),
            ]);
            
            // Notification d'offre reçue
            if ($index < count($offers)) {
                Notification::create([
                    'type' => 'new_offer',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $client->id,
                    'data' => json_encode([
                        'title' => 'Nouvelle offre reçue',
                        'message' => 'Vous avez reçu une nouvelle offre pour votre demande. Consultez-la dès maintenant !',
                        'offer_id' => $offers[$index]->id,
                    ]),
                    'read_at' => $index % 2 === 0 ? null : Carbon::now()->subDays(rand(1, 3)),
                    'created_at' => Carbon::now()->subDays(rand(1, 7)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 3)),
                ]);
            }
            
            // Notification de message reçu (utilisant la nouvelle classe)
            if (!$prestataires->isEmpty()) {
                $randomPrestataire = $prestataires->random();
                Notification::create([
                    'type' => NewMessageNotification::class,
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $client->id,
                    'data' => json_encode([
                        'title' => 'Nouveau message reçu',
                        'message' => 'Vous avez reçu un nouveau message de ' . $randomPrestataire->name . '. Consultez votre messagerie pour y répondre.',
                        'sender_name' => $randomPrestataire->name,
                        'sender_id' => $randomPrestataire->id,
                        'url' => '/messages',
                        'type' => 'new_message'
                    ]),
                    'read_at' => $index % 3 === 0 ? null : Carbon::now()->subHours(rand(1, 24)),
                    'created_at' => Carbon::now()->subDays(rand(1, 3)),
                    'updated_at' => Carbon::now()->subHours(rand(1, 24)),
                ]);
            }
        }
        
        // Notifications pour les prestataires
        foreach ($prestataires as $index => $prestataire) {
            // Notification de bienvenue
            Notification::create([
                'type' => 'welcome',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $prestataire->id,
                'data' => json_encode([
                    'title' => 'Bienvenue sur TaPrestation',
                    'message' => 'Merci de vous être inscrit comme prestataire. Complétez votre profil pour commencer à recevoir des demandes !',
                ]),
                'read_at' => $index === 0 ? null : Carbon::now()->subDays(rand(1, 5)),
                'created_at' => Carbon::now()->subDays(rand(5, 10)),
                'updated_at' => Carbon::now()->subDays(rand(1, 5)),
            ]);
            
            // Notification de nouvelle demande (utilisant la nouvelle classe)
            if (!$clients->isEmpty()) {
                $randomClient = $clients->random();
                Notification::create([
                    'type' => NewClientRequestNotification::class,
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $prestataire->id,
                    'data' => json_encode([
                        'title' => 'Nouvelle demande de service',
                        'message' => 'Une nouvelle demande de ' . $randomClient->name . ' correspondant à vos services est disponible. Budget: ' . (500 + $index * 100) . '€',
                        'client_name' => $randomClient->name,
                        'client_id' => $randomClient->id,
                        'request_title' => 'Développement d\'application web',
                        'budget' => 500 + $index * 100,
                        'description' => 'Recherche un développeur expérimenté pour créer une application web moderne.',
                        'url' => '/prestataire/requests',
                        'type' => 'new_request'
                    ]),
                    'read_at' => $index % 2 === 0 ? null : Carbon::now()->subDays(rand(1, 3)),
                    'created_at' => Carbon::now()->subDays(rand(1, 7)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 3)),
                ]);
            }
            
            // Notification d'offre acceptée (utilisant la nouvelle classe)
            if (!$offers->isEmpty() && !$clients->isEmpty()) {
                $randomClient = $clients->random();
                Notification::create([
                    'type' => OfferAcceptedNotification::class,
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $prestataire->id,
                    'data' => json_encode([
                        'title' => 'Offre acceptée',
                        'message' => 'Félicitations ! Votre offre a été acceptée par ' . $randomClient->name . '. Contactez-le pour commencer le travail.',
                        'client_name' => $randomClient->name,
                        'offer_price' => 750 + $index * 100,
                        'url' => '/prestataire/responses',
                        'type' => 'offer_accepted'
                    ]),
                    'read_at' => $index % 3 === 0 ? null : Carbon::now()->subHours(rand(1, 24)),
                    'created_at' => Carbon::now()->subDays(rand(1, 3)),
                    'updated_at' => Carbon::now()->subHours(rand(1, 24)),
                ]);
            }
            
            // Notification de nouvelle évaluation (utilisant la nouvelle classe)
            if (!$clients->isEmpty()) {
                $randomClient = $clients->random();
                $rating = rand(4, 5);
                Notification::create([
                    'type' => NewReviewNotification::class,
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $prestataire->id,
                    'data' => json_encode([
                        'title' => 'Nouvelle évaluation reçue',
                        'message' => $randomClient->name . ' a laissé une évaluation ' . $rating . '/5 étoiles sur votre service. Consultez votre profil pour la voir.',
                        'client_name' => $randomClient->name,
                        'rating' => $rating,
                        'url' => '/prestataire/profile',
                        'type' => 'new_review'
                    ]),
                    'read_at' => $index % 2 === 0 ? null : Carbon::now()->subDays(rand(1, 2)),
                    'created_at' => Carbon::now()->subDays(rand(1, 5)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 2)),
                ]);
            }
            
            // Notification de statut d'annonce (approuvée/rejetée)
            if ($index < 2) {
                $status = $index === 0 ? 'approved' : 'rejected';
                $statusText = $status === 'approved' ? 'approuvée' : 'rejetée';
                Notification::create([
                    'type' => AnnouncementStatusNotification::class,
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $prestataire->id,
                    'data' => json_encode([
                        'title' => 'Statut de votre annonce',
                        'message' => 'Votre annonce "Service de développement web" a été ' . $statusText . ' par l\'administration.',
                        'announcement_title' => 'Service de développement web',
                        'status' => $status,
                        'admin_comment' => $status === 'approved' ? 'Votre annonce respecte nos conditions.' : 'Votre annonce ne respecte pas nos conditions de publication.',
                        'url' => '/prestataire/announcements',
                        'type' => 'announcement_status'
                    ]),
                    'read_at' => null,
                    'created_at' => Carbon::now()->subHours(rand(2, 12)),
                    'updated_at' => Carbon::now()->subHours(rand(2, 12)),
                ]);
            }
        }
        
        // Notifications pour l'administrateur
        if ($admin && !$prestataires->isEmpty()) {
            $randomPrestataire = $prestataires->random();
            Notification::create([
                'type' => PrestataireApprovedNotification::class,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'title' => 'Nouveau prestataire à approuver',
                    'message' => 'Le prestataire ' . $randomPrestataire->name . ' s\'est inscrit et attend votre approbation. Vérifiez ses informations.',
                    'prestataire_name' => $randomPrestataire->name,
                    'prestataire_id' => $randomPrestataire->id,
                    'url' => '/admin/prestataires',
                    'type' => 'new_prestataire'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ]);
        }
        
        if ($admin && !$clients->isEmpty()) {
            Notification::create([
                'type' => 'reported_review',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'title' => 'Évaluation signalée',
                    'message' => 'Une évaluation a été signalée pour contenu inapproprié. Veuillez la vérifier et prendre les mesures nécessaires.',
                ]),
                'read_at' => Carbon::now()->subHours(12),
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subHours(12),
            ]);
        }
    }
}

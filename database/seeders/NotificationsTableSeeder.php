<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

use App\Models\Offer;
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
            
            // Notification de message reçu
            if (!$prestataires->isEmpty()) {
                Notification::create([
                    'type' => 'new_message',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $client->id,
                    'data' => json_encode([
                        'title' => 'Nouveau message',
                        'message' => 'Vous avez reçu un nouveau message. Consultez votre messagerie pour y répondre.',
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
            
            // Notification de nouvelle demande
            Notification::create([
                'type' => 'new_request',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $prestataire->id,
                'data' => json_encode([
                    'title' => 'Nouvelle demande de service',
                    'message' => 'Une nouvelle demande correspondant à vos services est disponible. Consultez-la et faites une offre !',
                ]),
                'read_at' => $index % 2 === 0 ? null : Carbon::now()->subDays(rand(1, 3)),
                'created_at' => Carbon::now()->subDays(rand(1, 7)),
                'updated_at' => Carbon::now()->subDays(rand(1, 3)),
            ]);
            
            // Notification d'offre acceptée
            if (!$offers->isEmpty()) {
                Notification::create([
                    'type' => 'offer_accepted',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $prestataire->id,
                    'data' => json_encode([
                        'title' => 'Offre acceptée',
                        'message' => 'Félicitations ! Votre offre a été acceptée par le client. Contactez-le pour commencer le travail.',
                    ]),
                    'read_at' => $index % 3 === 0 ? null : Carbon::now()->subHours(rand(1, 24)),
                    'created_at' => Carbon::now()->subDays(rand(1, 3)),
                    'updated_at' => Carbon::now()->subHours(rand(1, 24)),
                ]);
            }
            
            // Notification de nouvelle évaluation
            if (!$clients->isEmpty()) {
                Notification::create([
                    'type' => 'new_review',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $prestataire->id,
                    'data' => json_encode([
                        'title' => 'Nouvelle évaluation reçue',
                        'message' => 'Un client a laissé une évaluation sur votre service. Consultez votre profil pour la voir.',
                    ]),
                    'read_at' => $index % 2 === 0 ? null : Carbon::now()->subDays(rand(1, 2)),
                    'created_at' => Carbon::now()->subDays(rand(1, 5)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 2)),
                ]);
            }
        }
        
        // Notifications pour l'administrateur
        if ($admin && !$prestataires->isEmpty()) {
            Notification::create([
                'type' => 'new_prestataire',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'title' => 'Nouveau prestataire à approuver',
                    'message' => 'Un nouveau prestataire s\'est inscrit et attend votre approbation. Vérifiez ses informations.',
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

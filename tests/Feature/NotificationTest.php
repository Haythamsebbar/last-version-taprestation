<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Prestataire;
use App\Models\Offer;
use App\Models\ClientRequest;
use App\Notifications\NewOfferNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que les notifications sont correctement créées et affichées
     */
    public function test_notifications_are_created_and_displayed()
    {
        // Créer un utilisateur client
        $user = User::factory()->create([
            'email' => 'client@test.com',
            'password' => bcrypt('password')
        ]);
        
        $client = Client::factory()->create([
            'user_id' => $user->id
        ]);
        
        // Créer quelques notifications de test directement en base
        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\\Notifications\\NewOfferNotification',
            'data' => json_encode([
                'title' => 'Test Notification 1',
                'message' => 'Ceci est une notification de test',
                'offer_id' => 1,
                'url' => '/test-url'
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\\Notifications\\OfferAcceptedNotification',
            'data' => json_encode([
                'title' => 'Test Notification 2',
                'message' => 'Votre offre a été acceptée',
                'offer_id' => 2,
                'url' => '/test-url-2'
            ]),
            'read_at' => now()->subHour(),
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHour()
        ]);
        
        // Se connecter en tant que client
        $this->actingAs($user);
        
        // Tester l'accès à la page des notifications
        $response = $this->get('/notifications');
        
        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
        $response->assertViewHas('notifications');
        
        // Vérifier que les notifications sont présentes dans la vue
        $notifications = $response->viewData('notifications');
        $this->assertCount(2, $notifications);
        
        // Vérifier le contenu de la page
        $response->assertSee('Test Notification 1');
        $response->assertSee('Test Notification 2');
        $response->assertSee('Ceci est une notification de test');
        $response->assertSee('Votre offre a été acceptée');
        
        // Vérifier que le badge "Nouveau" apparaît pour les notifications non lues
        $response->assertSee('Nouveau');
        

    }
    
    /**
     * Test de la page notifications quand aucune notification n'existe
     */
    public function test_empty_notifications_page()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $response = $this->get('/notifications');
        
        $response->assertStatus(200);
        $response->assertSee('Vous êtes à jour !');
        $response->assertSee('Aucune nouvelle notification');
        

    }
    
    /**
     * Test de marquage d'une notification comme lue
     */
    public function test_mark_notification_as_read()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        
        // Créer une notification non lue
        $notification = $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\\Notifications\\NewOfferNotification',
            'data' => json_encode([
                'title' => 'Test Notification',
                'message' => 'Test message',
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $this->actingAs($user);
        
        // Marquer comme lue
        $response = $this->post("/notifications/{$notification->id}/mark-as-read");
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Vérifier que la notification est maintenant marquée comme lue
        $notification->refresh();
        $this->assertNotNull($notification->read_at);
        

    }
}
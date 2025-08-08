<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Client;
use App\Models\Prestataire;
use App\Models\Category;

class SimpleInscriptionTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function test_page_inscription_se_charge_correctement()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('Inscription');
    }

    /** @test */
    public function test_inscription_client_avec_donnees_minimales()
    {
        $clientData = [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ];

        $response = $this->post('/register', $clientData);

        // Vérifier que l'inscription ne génère pas d'erreur
        $response->assertStatus(302); // Redirection attendue
        
        // Vérifier que l'utilisateur est créé
        $this->assertDatabaseHas('users', [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@test.com',
            'role' => 'client',
        ]);
    }

    /** @test */
    public function test_validation_nom_requis()
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function test_validation_email_requis()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_validation_mot_de_passe_requis()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type' => 'client',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function test_validation_type_utilisateur_requis()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors(['user_type']);
    }

    /** @test */
    public function test_validation_email_unique()
    {
        // Créer un utilisateur existant
        User::create([
            'name' => 'Utilisateur Existant',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        $response = $this->post('/register', [
            'name' => 'Nouveau Utilisateur',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_validation_mot_de_passe_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'DifferentPassword',
            'user_type' => 'client',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function test_validation_mot_de_passe_complexite()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'simple',
            'password_confirmation' => 'simple',
            'user_type' => 'client',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function test_bouton_inscription_client_traite_donnees()
    {
        $response = $this->post('/register', [
            'name' => 'Client Bouton Test',
            'email' => 'client.bouton@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ]);

        // Vérifier que le bouton traite les données (pas d'erreur 405 Method Not Allowed)
        $response->assertStatus(302);
        
        // Vérifier que les données sont bien envoyées à la base
        $this->assertDatabaseHas('users', [
            'email' => 'client.bouton@test.com',
            'role' => 'client'
        ]);
    }

    /** @test */
    public function test_redirection_apres_inscription_client()
    {
        $response = $this->post('/register', [
            'name' => 'Client Redirection',
            'email' => 'client.redirect@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ]);

        // Vérifier la redirection vers le dashboard client
        $response->assertRedirect('/client/dashboard');
    }

    /** @test */
    public function test_connexion_automatique_apres_inscription()
    {
        $response = $this->post('/register', [
            'name' => 'Auto Login Test',
            'email' => 'autologin@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ]);

        // Vérifier que l'utilisateur est connecté après inscription
        $this->assertAuthenticated();
        
        // Vérifier que c'est le bon utilisateur
        $user = User::where('email', 'autologin@test.com')->first();
        $this->assertEquals($user->id, auth()->id());
    }

    /** @test */
    public function test_message_succes_apres_inscription()
    {
        $response = $this->post('/register', [
            'name' => 'Success Message Test',
            'email' => 'success@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ]);

        // Vérifier le message de succès
        $response->assertSessionHas('success');
    }
}
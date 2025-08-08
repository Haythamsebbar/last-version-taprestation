<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Client;
use App\Models\Prestataire;
use App\Models\Category;

class RegistrationCompleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer des catégories de test
        $this->mainCategory = Category::create([
            'name' => 'Plomberie',
            'parent_id' => null,
        ]);
        
        $this->subCategory = Category::create([
            'name' => 'Réparation',
            'parent_id' => $this->mainCategory->id,
        ]);
        
        Storage::fake('public');
    }

    /** @test */
    public function test_page_inscription_affiche_formulaires_correctement()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('Inscription Client');
        $response->assertSee('Inscription Prestataire');
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="user_type"', false);
    }

    /** @test */
    public function test_inscription_client_avec_donnees_valides()
    {
        $clientPhoto = UploadedFile::fake()->image('client_photo.jpg');
        
        $clientData = [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
            'location' => 'Paris, France',
            'client_profile_photo' => $clientPhoto,
        ];

        $response = $this->post('/register', $clientData);

        // Vérifier la redirection
        $response->assertRedirect('/client/dashboard');
        $response->assertSessionHas('success', 'Inscription réussie ! Bienvenue sur votre espace client.');

        // Vérifier que l'utilisateur est créé en base
        $this->assertDatabaseHas('users', [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'role' => 'client',
        ]);

        // Vérifier que le client est créé en base
        $user = User::where('email', 'jean.dupont@example.com')->first();
        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
            'location' => 'Paris, France',
        ]);

        // Vérifier que la photo est stockée
        $client = Client::where('user_id', $user->id)->first();
        $this->assertNotNull($client->photo);
        Storage::disk('public')->assertExists($client->photo);

        // Vérifier que l'utilisateur est connecté
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function test_inscription_prestataire_avec_donnees_valides()
    {
        $prestatairePhoto = UploadedFile::fake()->image('prestataire_photo.jpg');
        
        $prestataireData = [
            'name' => 'Marie Martin',
            'email' => 'marie.martin@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'prestataire',
            'company_name' => 'Plomberie Martin',
            'phone' => '0123456789',
            'category_id' => $this->mainCategory->id,
            'subcategory_id' => $this->subCategory->id,
            'city' => 'Lyon',
            'prestataire_profile_photo' => $prestatairePhoto,
            'description' => 'Plombier expérimenté',
            'portfolio_url' => 'https://portfolio.example.com',
        ];

        $response = $this->post('/register', $prestataireData);

        // Vérifier la redirection
        $response->assertRedirect('/prestataire/dashboard');
        $response->assertSessionHas('success', 'Inscription réussie ! Bienvenue sur votre espace prestataire.');

        // Vérifier que l'utilisateur est créé en base
        $this->assertDatabaseHas('users', [
            'name' => 'Marie Martin',
            'email' => 'marie.martin@example.com',
            'role' => 'prestataire',
        ]);

        // Vérifier que le prestataire est créé en base
        $user = User::where('email', 'marie.martin@example.com')->first();
        $this->assertDatabaseHas('prestataires', [
            'user_id' => $user->id,
            'company_name' => 'Plomberie Martin',
            'phone' => '0123456789',
            'city' => 'Lyon',
            'secteur_activite' => 'Plomberie',
            'competences' => 'Réparation',
            'description' => 'Plombier expérimenté',
            'portfolio_url' => 'https://portfolio.example.com',
        ]);

        // Vérifier que la photo est stockée
        $prestataire = Prestataire::where('user_id', $user->id)->first();
        $this->assertNotNull($prestataire->photo);
        Storage::disk('public')->assertExists($prestataire->photo);

        // Vérifier que l'utilisateur est connecté
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function test_validation_erreurs_champs_requis_client()
    {
        $response = $this->post('/register', [
            'user_type' => 'client',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('clients', 0);
    }

    /** @test */
    public function test_validation_erreurs_champs_requis_prestataire()
    {
        $response = $this->post('/register', [
            'user_type' => 'prestataire',
        ]);

        $response->assertSessionHasErrors([
            'name', 'email', 'password', 'company_name', 
            'phone', 'category_id', 'city', 'prestataire_profile_photo'
        ]);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('prestataires', 0);
    }

    /** @test */
    public function test_email_duplique_rejete()
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
        $this->assertDatabaseCount('users', 1);
    }

    /** @test */
    public function test_mot_de_passe_faible_rejete()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'faible',
            'password_confirmation' => 'faible',
            'user_type' => 'client',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseCount('users', 0);
    }

    /** @test */
    public function test_photo_invalide_rejetee_prestataire()
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);
        
        $response = $this->post('/register', [
            'name' => 'Test Prestataire',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'prestataire',
            'company_name' => 'Test Company',
            'phone' => '0123456789',
            'category_id' => $this->mainCategory->id,
            'city' => 'Test City',
            'prestataire_profile_photo' => $invalidFile,
        ]);

        $response->assertSessionHasErrors(['prestataire_profile_photo']);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('prestataires', 0);
    }

    /** @test */
    public function test_categorie_inexistante_rejetee()
    {
        $prestatairePhoto = UploadedFile::fake()->image('photo.jpg');
        
        $response = $this->post('/register', [
            'name' => 'Test Prestataire',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'prestataire',
            'company_name' => 'Test Company',
            'phone' => '0123456789',
            'category_id' => 999, // ID inexistant
            'city' => 'Test City',
            'prestataire_profile_photo' => $prestatairePhoto,
        ]);

        $response->assertSessionHasErrors(['category_id']);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('prestataires', 0);
    }

    /** @test */
    public function test_bouton_inscription_client_fonctionne()
    {
        $clientPhoto = UploadedFile::fake()->image('client.jpg');
        
        $response = $this->post('/register', [
            'name' => 'Client Test',
            'email' => 'client@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
            'location' => 'Test Location',
            'client_profile_photo' => $clientPhoto,
        ]);

        // Vérifier que le bouton d'inscription fonctionne (redirection réussie)
        $response->assertRedirect('/client/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'client@test.com']);
    }

    /** @test */
    public function test_bouton_inscription_prestataire_fonctionne()
    {
        $prestatairePhoto = UploadedFile::fake()->image('prestataire.jpg');
        
        $response = $this->post('/register', [
            'name' => 'Prestataire Test',
            'email' => 'prestataire@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'prestataire',
            'company_name' => 'Test Company',
            'phone' => '0123456789',
            'category_id' => $this->mainCategory->id,
            'city' => 'Test City',
            'prestataire_profile_photo' => $prestatairePhoto,
        ]);

        // Vérifier que le bouton d'inscription fonctionne (redirection réussie)
        $response->assertRedirect('/prestataire/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'prestataire@test.com']);
    }

    /** @test */
    public function test_api_categories_principales_fonctionne()
    {
        $response = $this->get('/categories/main');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name']
        ]);
    }

    /** @test */
    public function test_api_sous_categories_fonctionne()
    {
        $response = $this->get('/categories/' . $this->mainCategory->id . '/subcategories');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name']
        ]);
    }
}
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Prestataire;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer des catégories de test
        Category::create([
            'name' => 'Plomberie',
            'description' => 'Services de plomberie',
            'parent_id' => null
        ]);
        
        Category::create([
            'name' => 'Réparation',
            'description' => 'Réparation de plomberie',
            'parent_id' => 1
        ]);
    }

    /** @test */
    public function test_client_registration_form_displays_correctly()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('S\'inscrire en tant que Client');
        $response->assertSee('S\'inscrire en tant que Prestataire');
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="password_confirmation"', false);
    }

    /** @test */
    public function test_client_registration_with_valid_data()
    {
        Storage::fake('public');
        
        $profilePhoto = UploadedFile::fake()->image('profile.jpg');
        
        $clientData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0123456789',
            'address' => '123 Rue de la Paix, Paris',
            'latitude' => '48.8566',
            'longitude' => '2.3522',
            'profile_photo' => $profilePhoto,
            'user_type' => 'client'
        ];

        $response = $this->post('/register', $clientData);

        // Vérifier la redirection
        $response->assertRedirect('/dashboard');

        // Vérifier que l'utilisateur a été créé
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'user_type' => 'client'
        ]);

        // Vérifier que le client a été créé
        $user = User::where('email', 'john@example.com')->first();
        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
            'phone' => '0123456789',
            'address' => '123 Rue de la Paix, Paris',
            'latitude' => '48.8566',
            'longitude' => '2.3522'
        ]);

        // Vérifier que la photo de profil a été uploadée
        $client = Client::where('user_id', $user->id)->first();
        $this->assertNotNull($client->profile_photo);
        Storage::disk('public')->assertExists($client->profile_photo);
    }

    /** @test */
    public function test_prestataire_registration_with_valid_data()
    {
        Storage::fake('public');
        
        $profilePhoto = UploadedFile::fake()->image('profile.jpg');
        
        $prestataireData = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0987654321',
            'address' => '456 Avenue des Champs, Lyon',
            'latitude' => '45.7640',
            'longitude' => '4.8357',
            'category_id' => 1,
            'subcategory_id' => 2,
            'description' => 'Expert en plomberie avec 10 ans d\'expérience',
            'portfolio_url' => 'https://portfolio.example.com',
            'profile_photo' => $profilePhoto,
            'user_type' => 'prestataire'
        ];

        $response = $this->post('/register', $prestataireData);

        // Vérifier la redirection
        $response->assertRedirect('/dashboard');

        // Vérifier que l'utilisateur a été créé
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'user_type' => 'prestataire'
        ]);

        // Vérifier que le prestataire a été créé
        $user = User::where('email', 'jane@example.com')->first();
        $this->assertDatabaseHas('prestataires', [
            'user_id' => $user->id,
            'phone' => '0987654321',
            'address' => '456 Avenue des Champs, Lyon',
            'latitude' => '45.7640',
            'longitude' => '4.8357',
            'category_id' => 1,
            'subcategory_id' => 2,
            'description' => 'Expert en plomberie avec 10 ans d\'expérience',
            'portfolio_url' => 'https://portfolio.example.com'
        ]);

        // Vérifier que la photo de profil a été uploadée
        $prestataire = Prestataire::where('user_id', $user->id)->first();
        $this->assertNotNull($prestataire->profile_photo);
        Storage::disk('public')->assertExists($prestataire->profile_photo);
    }

    /** @test */
    public function test_client_registration_validation_errors()
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
            'user_type' => 'client'
        ];

        $response = $this->post('/register', $invalidData);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'password'
        ]);
    }

    /** @test */
    public function test_prestataire_registration_validation_errors()
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
            'category_id' => 999, // Catégorie inexistante
            'user_type' => 'prestataire'
        ];

        $response = $this->post('/register', $invalidData);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'password',
            'category_id'
        ]);
    }

    /** @test */
    public function test_duplicate_email_registration()
    {
        // Créer un utilisateur existant
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'user_type' => 'client'
        ]);

        $duplicateData = [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'client'
        ];

        $response = $this->post('/register', $duplicateData);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_categories_api_endpoint()
    {
        $response = $this->get('/categories/main');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'description'
            ]
        ]);
    }

    /** @test */
    public function test_subcategories_api_endpoint()
    {
        $response = $this->get('/api/categories/1/subcategories');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'description',
                'parent_id'
            ]
        ]);
    }

    /** @test */
    public function test_geolocation_coordinates_are_saved()
    {
        $clientData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0123456789',
            'address' => 'Test Address',
            'latitude' => '48.8566',
            'longitude' => '2.3522',
            'user_type' => 'client'
        ];

        $response = $this->post('/register', $clientData);

        $user = User::where('email', 'test@example.com')->first();
        $client = Client::where('user_id', $user->id)->first();

        $this->assertEquals('48.8566', $client->latitude);
        $this->assertEquals('2.3522', $client->longitude);
    }

    /** @test */
    public function test_profile_photo_upload_validation()
    {
        Storage::fake('public');
        
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);
        
        $clientData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'profile_photo' => $invalidFile,
            'user_type' => 'client'
        ];

        $response = $this->post('/register', $clientData);

        $response->assertSessionHasErrors(['profile_photo']);
    }
}
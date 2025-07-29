<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PrestataireCreateFormsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $prestataire;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur prestataire
        $this->prestataire = User::factory()->create([
            'role' => 'prestataire',
            'email_verified_at' => now(),
        ]);
        
        // Créer une catégorie pour les tests
        $this->category = Category::factory()->create([
            'name' => 'Test Category',
            'type' => 'service'
        ]);
        
        Storage::fake('public');
    }

    /** @test */
    public function test_urgent_sales_create_view_loads_correctly()
    {
        $response = $this->actingAs($this->prestataire)
            ->get(route('prestataire.urgent-sales.create'));

        $response->assertStatus(200)
            ->assertViewIs('prestataire.urgent-sales.create')
            ->assertSee('Ajouter une Vente Urgente')
            ->assertSee('Titre de la vente')
            ->assertSee('Prix (€)')
            ->assertSee('État')
            ->assertSee('Quantité')
            ->assertSee('Localisation')
            ->assertSee('Description détaillée');
    }

    /** @test */
    public function test_urgent_sales_can_be_created_successfully()
    {
        $image = UploadedFile::fake()->image('product.jpg');
        
        $data = [
            'title' => 'Vente urgente test',
            'price' => 150.50,
            'condition' => 'good',
            'quantity' => 2,
            'location' => 'Paris, France',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'description' => 'Description détaillée de la vente urgente pour le test',
            'photos' => [$image],
            'is_urgent' => 1,
            'status' => 'active'
        ];

        $response = $this->actingAs($this->prestataire)
            ->post(route('prestataire.urgent-sales.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('urgent_sales', [
            'title' => 'Vente urgente test',
            'price' => 150.50,
            'condition' => 'good',
            'quantity' => 2,
            'user_id' => $this->prestataire->id
        ]);
    }

    /** @test */
    public function test_services_create_view_loads_correctly()
    {
        $response = $this->actingAs($this->prestataire)
            ->get(route('prestataire.services.create'));

        $response->assertStatus(200)
            ->assertViewIs('prestataire.services.create')
            ->assertSee('Créer un nouveau service')
            ->assertSee('Titre du service')
            ->assertSee('Description détaillée')
            ->assertSee('Prix du service')
            ->assertSee('Catégorie du service')
            ->assertSee('Localisation');
    }

    /** @test */
    public function test_service_can_be_created_successfully()
    {
        $image = UploadedFile::fake()->image('service.jpg');
        
        $data = [
            'title' => 'Service de test',
            'description' => 'Description détaillée du service de test avec toutes les informations nécessaires',
            'price' => 75.00,
            'price_type' => 'heure',
            'category_id' => $this->category->id,
            'delivery_time' => 5,
            'reservable' => 1,
            'address' => 'Test Address, Paris',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'images' => [$image]
        ];

        $response = $this->actingAs($this->prestataire)
            ->post(route('prestataire.services.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('services', [
            'title' => 'Service de test',
            'price' => 75.00,
            'price_type' => 'heure',
            'category_id' => $this->category->id,
            'user_id' => $this->prestataire->id
        ]);
    }

    /** @test */
    public function test_equipment_create_view_loads_correctly()
    {
        $response = $this->actingAs($this->prestataire)
            ->get(route('prestataire.equipment.create'));

        $response->assertStatus(200)
            ->assertViewIs('prestataire.equipment.create')
            ->assertSee('Ajouter un équipement')
            ->assertSee('Nom de l\'équipement')
            ->assertSee('Catégorie')
            ->assertSee('Localisation')
            ->assertSee('Description courte')
            ->assertSee('Prix par jour')
            ->assertSee('Caution');
    }

    /** @test */
    public function test_equipment_can_be_created_successfully()
    {
        $image = UploadedFile::fake()->image('equipment.jpg');
        
        $data = [
            'name' => 'Perceuse test',
            'category_id' => $this->category->id,
            'description' => 'Description détaillée de la perceuse de test avec toutes ses caractéristiques',
            'technical_specifications' => 'Puissance: 800W, Vitesse: 3000 rpm',
            'price_per_day' => 25.00,
            'security_deposit' => 100.00,
            'price_per_hour' => 5.00,
            'price_per_week' => 150.00,
            'price_per_month' => 500.00,
            'address' => 'Test Address',
            'city' => 'Paris',
            'country' => 'France',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'condition' => 'excellent',
            'delivery_included' => 1,
            'license_required' => 0,
            'is_available' => 1,
            'rental_conditions' => 'Conditions de location spécifiques',
            'available_from' => now()->format('Y-m-d'),
            'available_until' => now()->addMonths(6)->format('Y-m-d'),
            'main_photo' => $image
        ];

        $response = $this->actingAs($this->prestataire)
            ->post(route('prestataire.equipment.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('equipment', [
            'name' => 'Perceuse test',
            'price_per_day' => 25.00,
            'security_deposit' => 100.00,
            'city' => 'Paris',
            'country' => 'France',
            'user_id' => $this->prestataire->id
        ]);
    }

    /** @test */
    public function test_urgent_sales_form_validation_works()
    {
        $response = $this->actingAs($this->prestataire)
            ->post(route('prestataire.urgent-sales.store'), []);

        $response->assertSessionHasErrors([
            'title',
            'price',
            'condition',
            'quantity',
            'location',
            'description'
        ]);
    }

    /** @test */
    public function test_services_form_validation_works()
    {
        $response = $this->actingAs($this->prestataire)
            ->post(route('prestataire.services.store'), []);

        $response->assertSessionHasErrors([
            'title',
            'description',
            'category_id'
        ]);
    }

    /** @test */
    public function test_equipment_form_validation_works()
    {
        $response = $this->actingAs($this->prestataire)
            ->post(route('prestataire.equipment.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'category_id',
            'description',
            'price_per_day',
            'security_deposit',
            'city',
            'country',
            'main_photo'
        ]);
    }

    /** @test */
    public function test_unauthorized_user_cannot_access_create_forms()
    {
        // Test sans authentification
        $this->get(route('prestataire.urgent-sales.create'))
            ->assertRedirect(route('login'));
            
        $this->get(route('prestataire.services.create'))
            ->assertRedirect(route('login'));
            
        $this->get(route('prestataire.equipment.create'))
            ->assertRedirect(route('login'));

        // Test avec un utilisateur client
        $client = User::factory()->create(['role' => 'client']);
        
        $this->actingAs($client)
            ->get(route('prestataire.urgent-sales.create'))
            ->assertStatus(403);
            
        $this->actingAs($client)
            ->get(route('prestataire.services.create'))
            ->assertStatus(403);
            
        $this->actingAs($client)
            ->get(route('prestataire.equipment.create'))
            ->assertStatus(403);
    }
}
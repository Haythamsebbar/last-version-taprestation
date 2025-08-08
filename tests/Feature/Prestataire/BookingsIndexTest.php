<?php

namespace Tests\Feature\Prestataire;

use App\Models\User;
use App\Models\Prestataire;
use App\Models\Client;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\EquipmentRental;
use App\Models\EquipmentRentalRequest;
use App\Models\UrgentSale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class BookingsIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $prestataire;
    private $prestataireUser;
    private $client;
    private $clientUser;
    private $service;
    private $equipment;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur prestataire
        $this->prestataireUser = User::create([
            'role' => 'prestataire',
            'name' => 'Test Prestataire',
            'email' => 'prestataire@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);
        
        $this->prestataire = Prestataire::create([
            'user_id' => $this->prestataireUser->id,
            'company_name' => 'Test Company',
            'phone' => '0123456789',
            'address' => 'Test Address',
            'city' => 'Test City',
            'postal_code' => '12345',
            'country' => 'France'
        ]);
        
        // Créer un utilisateur client
        $this->clientUser = User::create([
            'role' => 'client',
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);
        
        $this->client = Client::create([
            'user_id' => $this->clientUser->id,
            'phone' => '0987654321',
            'address' => 'Client Address',
            'city' => 'Client City',
            'postal_code' => '54321'
        ]);
        
        // Créer un service
        $this->service = Service::create([
            'prestataire_id' => $this->prestataire->id,
            'title' => 'Service Test',
            'description' => 'Description du service test',
            'price' => 100,
            'price_type' => 'fixed',
            'status' => 'active'
        ]);
        
        // Créer un équipement
        $this->equipment = Equipment::create([
            'prestataire_id' => $this->prestataire->id,
            'name' => 'Équipement Test',
            'slug' => 'equipement-test',
            'description' => 'Description équipement test',
            'price_per_day' => 50,
            'condition' => 'excellent',
            'status' => 'active',
            'is_available' => true,
            'address' => 'Equipment Address',
            'city' => 'Equipment City',
            'postal_code' => '67890',
            'country' => 'France'
        ]);
    }

    /** @test */
    public function it_displays_bookings_index_page_successfully()
    {
        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(200)
            ->assertViewIs('prestataire.bookings.index')
            ->assertSee('Gestion des Activités')
            ->assertSee('Toutes les activités')
            ->assertSee('Réservations')
            ->assertSee('Équipements')
            ->assertSee('Ventes urgentes');
    }

    /** @test */
    public function it_displays_service_bookings_with_all_statuses()
    {
        // Créer des réservations avec différents statuts
        $bookingPending = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'pending',
            'start_datetime' => Carbon::now()->addDay(),
            'end_datetime' => Carbon::now()->addDay()->addHours(2),
            'total_price' => 100
        ]);
        
        $bookingConfirmed = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'confirmed',
            'start_datetime' => Carbon::now()->addDays(2),
            'end_datetime' => Carbon::now()->addDays(2)->addHours(2),
            'total_price' => 100
        ]);
        
        $bookingCompleted = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'completed',
            'start_datetime' => Carbon::now()->subDay(),
            'end_datetime' => Carbon::now()->subDay()->addHours(2),
            'total_price' => 100
        ]);
        
        $bookingCancelled = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'cancelled',
            'start_datetime' => Carbon::now()->addDays(3),
            'end_datetime' => Carbon::now()->addDays(3)->addHours(2),
            'total_price' => 100
        ]);
        
        $bookingRefused = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'refused',
            'start_datetime' => Carbon::now()->addDays(4),
            'end_datetime' => Carbon::now()->addDays(4)->addHours(2),
            'total_price' => 100
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(200)
            ->assertSee('RÉSERVATION')
            ->assertSee('Service Test')
            ->assertSee('Test Client')
            ->assertSee('En attente')
            ->assertSee('Confirmée')
            ->assertSee('Terminée')
            ->assertSee('Annulée')
            ->assertSee('Refusée');
    }

    /** @test */
    public function it_displays_equipment_rentals_with_all_statuses()
    {
        // Créer des demandes de location d'équipement d'abord
        $rentalRequestConfirmed = EquipmentRentalRequest::create([
            'request_number' => 'REQ-CONF-' . uniqid(),
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'start_date' => Carbon::now()->addDay(),
            'end_date' => Carbon::now()->addDays(3),
            'duration_days' => 3,
            'unit_price' => 50,
            'total_amount' => 150,
            'final_amount' => 150,
            'status' => 'accepted'
        ]);
        
        $rentalRequestActive = EquipmentRentalRequest::create([
            'request_number' => 'REQ-ACT-' . uniqid(),
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(2),
            'duration_days' => 2,
            'unit_price' => 50,
            'total_amount' => 100,
            'final_amount' => 100,
            'status' => 'accepted'
        ]);
        
        $rentalRequestCompleted = EquipmentRentalRequest::create([
            'request_number' => 'REQ-COMP-' . uniqid(),
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'start_date' => Carbon::now()->subDays(3),
            'end_date' => Carbon::now()->subDay(),
            'duration_days' => 2,
            'unit_price' => 50,
            'total_amount' => 100,
            'final_amount' => 100,
            'status' => 'accepted'
        ]);
        
        // Créer des locations d'équipement avec différents statuts
        $rentalConfirmed = EquipmentRental::create([
            'rental_number' => 'LOC-CONF-' . uniqid(),
            'rental_request_id' => $rentalRequestConfirmed->id,
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'confirmed',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(3),
            'planned_duration_days' => 3,
            'unit_price' => 50,
            'base_amount' => 150,
            'total_amount' => 150,
            'final_amount' => 150,
        ]);
        
        $rentalActive = EquipmentRental::create([
            'rental_number' => 'LOC-ACT-' . uniqid(),
            'rental_request_id' => $rentalRequestActive->id,
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'delivered',
            'start_date' => now(),
            'end_date' => now()->addDays(2),
            'planned_duration_days' => 2,
            'unit_price' => 50,
            'base_amount' => 100,
            'total_amount' => 100,
            'final_amount' => 100
        ]);
        
        $rentalCompleted = EquipmentRental::create([
            'rental_number' => 'LOC-COMP-' . uniqid(),
            'rental_request_id' => $rentalRequestCompleted->id,
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'completed',
            'start_date' => Carbon::now()->subDays(3),
            'end_date' => Carbon::now()->subDay(),
            'planned_duration_days' => 2,
            'unit_price' => 50,
            'base_amount' => 100,
            'total_amount' => 100,
            'final_amount' => 100
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index', ['type' => 'equipment']));

        $response->assertStatus(200)
            ->assertSee('ÉQUIPEMENT')
            ->assertSee('Équipement Test')
            ->assertSee('Test Client')
            ->assertSee('Confirmée')
            ->assertSee('Livrée')
            ->assertSee('Terminée');
    }

    /** @test */
    public function it_displays_equipment_rental_requests_with_all_statuses()
    {
        // Créer des demandes de location avec différents statuts
        $requestPending = EquipmentRentalRequest::create([
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'request_number' => 'REQ-' . strtoupper(uniqid()),
            'status' => 'pending',
            'start_date' => Carbon::now()->addDay(),
            'end_date' => Carbon::now()->addDays(3),
            'total_amount' => 120,
            'final_amount' => 120,
            'duration_days' => 3,
            'unit_price' => 40
        ]);
        
        $requestAccepted = EquipmentRentalRequest::create([
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'request_number' => 'REQ-' . strtoupper(uniqid()),
            'status' => 'accepted',
            'start_date' => Carbon::now()->addDays(2),
            'end_date' => Carbon::now()->addDays(4),
            'total_amount' => 130,
            'final_amount' => 130,
            'duration_days' => 3,
            'unit_price' => 43
        ]);
        
        $requestRejected = EquipmentRentalRequest::create([
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'request_number' => 'REQ-' . strtoupper(uniqid()),
            'status' => 'rejected',
            'start_date' => Carbon::now()->addDays(5),
            'end_date' => Carbon::now()->addDays(7),
            'total_amount' => 110,
            'final_amount' => 110,
            'duration_days' => 3,
            'unit_price' => 37
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index', ['type' => 'equipment']));

        $response->assertStatus(200)
            ->assertSee('DEMANDE DE LOCATION')
            ->assertSee('Équipement Test')
            ->assertSee('Test Client')
            ->assertSee('En attente')
            ->assertSee('Acceptée')
            ->assertSee('Refusée');
    }

    /** @test */
    public function it_displays_urgent_sales_with_all_statuses()
    {
        // Créer des ventes urgentes avec différents statuts
        $saleActive = UrgentSale::create([
            'prestataire_id' => $this->prestataire->id,
            'title' => 'Vente Urgente Test',
            'description' => 'Description vente urgente',
            'price' => 200,
            'status' => 'active',
            'category' => 'equipment',
            'condition' => 'good'
        ]);
        
        $saleSold = UrgentSale::create([
            'prestataire_id' => $this->prestataire->id,
            'title' => 'Vente Vendue',
            'description' => 'Description vente vendue',
            'price' => 150,
            'status' => 'sold',
            'category' => 'equipment',
            'condition' => 'good'
        ]);
        
        $saleWithdrawn = UrgentSale::create([
            'prestataire_id' => $this->prestataire->id,
            'title' => 'Vente Retirée',
            'description' => 'Description vente retirée',
            'price' => 180,
            'status' => 'withdrawn',
            'category' => 'equipment',
            'condition' => 'good'
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index', ['type' => 'urgent_sales']));

        $response->assertStatus(200)
            ->assertSee('VENTE URGENTE')
            ->assertSee('Vente Urgente Test')
            ->assertSee('Vente Vendue')
            ->assertSee('Vente Retirée')
            ->assertSee('Active')
            ->assertSee('Vendue')
            ->assertSee('Retirée');
    }

    /** @test */
    public function it_filters_bookings_by_status()
    {
        // Créer des réservations avec différents statuts
        $bookingPending = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'pending',
            'start_datetime' => Carbon::now()->addDay(),
            'end_datetime' => Carbon::now()->addDay()->addHours(2),
            'total_price' => 100
        ]);
        
        $bookingConfirmed = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'confirmed',
            'start_datetime' => Carbon::now()->addDays(2),
            'end_datetime' => Carbon::now()->addDays(2)->addHours(2),
            'total_price' => 100
        ]);

        // Filtrer par statut 'pending' avec type 'bookings'
        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index', ['status' => 'pending', 'type' => 'bookings']));

        $response->assertStatus(200)
            ->assertSee('En attente');
        
        // Vérifier que seules les réservations 'pending' sont affichées dans les données
        $content = $response->getContent();
        
        // Vérifier qu'il n'y a pas de badge de statut 'Confirmée' dans les données de réservation
        $this->assertStringNotContainsString('<i class="fas fa-check-circle mr-1"></i> Confirmée', $content);
        
        // Vérifier que le statut 'En attente' est présent
        $this->assertStringContainsString('<i class="fas fa-clock mr-1"></i> En attente', $content);
    }

    /** @test */
    public function it_filters_by_date_range()
    {
        // Créer des réservations à différentes dates
        $bookingToday = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'start_datetime' => Carbon::today(),
            'end_datetime' => Carbon::today()->addHours(2),
            'total_price' => 100
        ]);
        
        $bookingFuture = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'start_datetime' => Carbon::now()->addWeek(),
            'end_datetime' => Carbon::now()->addWeek()->addHours(2),
            'total_price' => 100
        ]);

        // Filtrer par 'today'
        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index', ['date_range' => 'today']));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_filters_by_service()
    {
        // Créer un autre service
        $anotherService = Service::create([
            'prestataire_id' => $this->prestataire->id,
            'title' => 'Autre Service',
            'description' => 'Description autre service',
            'price' => 150,
            'price_type' => 'fixed',
            'status' => 'active'
        ]);
        
        // Créer des réservations pour différents services
        $booking1 = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'start_datetime' => Carbon::now()->addDay(),
            'end_datetime' => Carbon::now()->addDay()->addHours(2),
            'total_price' => 100
        ]);
        
        $booking2 = Booking::create([
            'service_id' => $anotherService->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'start_datetime' => Carbon::now()->addDays(2),
            'end_datetime' => Carbon::now()->addDays(2)->addHours(2),
            'total_price' => 150
        ]);

        // Filtrer par service spécifique
        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index', ['service_id' => $this->service->id]));

        $response->assertStatus(200)
            ->assertSee('Service Test');
    }

    /** @test */
    public function it_displays_action_buttons_for_pending_bookings()
    {
        $booking = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'pending',
            'start_datetime' => Carbon::now()->addDay(),
            'end_datetime' => Carbon::now()->addDay()->addHours(2),
            'total_price' => 100
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(200)
            ->assertSee('Confirmer')
            ->assertSee('Refuser');
    }

    /** @test */
    public function it_displays_action_buttons_for_confirmed_bookings()
    {
        $booking = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'confirmed',
            'start_datetime' => Carbon::now()->addDay(),
            'end_datetime' => Carbon::now()->addDay()->addHours(2),
            'total_price' => 100
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(200)
            ->assertSee('Marquer terminé')
            ->assertSee('Annuler');
    }

    /** @test */
    public function it_displays_accept_and_reject_buttons_for_all_equipment_rental_requests()
    {
        // Créer des demandes avec différents statuts
        $requestPending = EquipmentRentalRequest::create([
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'request_number' => 'REQ-' . strtoupper(uniqid()),
            'status' => 'pending',
            'start_date' => Carbon::now()->addDay(),
            'end_date' => Carbon::now()->addDays(3),
            'total_amount' => 120,
            'final_amount' => 120,
            'duration_days' => 3,
            'unit_price' => 40
        ]);
        
        $requestAccepted = EquipmentRentalRequest::create([
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'request_number' => 'REQ-' . strtoupper(uniqid()),
            'status' => 'accepted',
            'start_date' => Carbon::now()->addDays(2),
            'end_date' => Carbon::now()->addDays(4),
            'total_amount' => 130,
            'final_amount' => 130,
            'duration_days' => 3,
            'unit_price' => 43
        ]);
        
        $requestRejected = EquipmentRentalRequest::create([
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'request_number' => 'REQ-' . strtoupper(uniqid()),
            'status' => 'rejected',
            'start_date' => Carbon::now()->addDays(5),
            'end_date' => Carbon::now()->addDays(7),
            'total_amount' => 110,
            'final_amount' => 110,
            'duration_days' => 3,
            'unit_price' => 37
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index', ['type' => 'equipment']));

        // Vérifier que les boutons sont affichés pour tous les statuts
        $response->assertStatus(200)
            ->assertSeeInOrder(['Accepter', 'Refuser']) // Pour pending
            ->assertSeeInOrder(['Accepter', 'Refuser']) // Pour accepted
            ->assertSeeInOrder(['Accepter', 'Refuser']); // Pour rejected
    }

    /** @test */
    public function it_displays_empty_state_when_no_activities()
    {
        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(200)
            ->assertSee('Aucune activité trouvée');
    }

    /** @test */
    public function it_displays_success_message_from_session()
    {
        $response = $this->actingAs($this->prestataireUser)
            ->withSession(['success' => 'Opération réussie'])
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(200)
            ->assertSee('Opération réussie');
    }

    /** @test */
    public function it_displays_error_message_from_session()
    {
        $response = $this->actingAs($this->prestataireUser)
            ->withSession(['error' => 'Une erreur est survenue'])
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(200)
            ->assertSee('Une erreur est survenue');
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->get(route('prestataire.bookings.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_requires_prestataire_role()
    {
        $clientUser = User::create([
            'role' => 'client',
            'name' => 'Client User',
            'email' => 'clientuser@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);
        
        $response = $this->actingAs($clientUser)
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_displays_client_notes_when_present()
    {
        $booking = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'client_notes' => 'Notes importantes du client',
            'start_datetime' => Carbon::now()->addDay(),
            'end_datetime' => Carbon::now()->addDay()->addHours(2),
            'total_price' => 100
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(200)
            ->assertSee('Notes du client')
            ->assertSee('Notes importantes du client');
    }

    /** @test */
    public function it_displays_cancellation_reason_when_present()
    {
        $booking = Booking::create([
            'service_id' => $this->service->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Raison d\'annulation',
            'start_datetime' => Carbon::now()->addDay(),
            'end_datetime' => Carbon::now()->addDay()->addHours(2),
            'total_price' => 100
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index'));

        $response->assertStatus(200)
            ->assertSee('Raison d\'annulation')
            ->assertSee('Raison d\'annulation');
    }

    /** @test */
    public function it_displays_view_details_button_for_all_requests()
    {
        $request = EquipmentRentalRequest::create([
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'request_number' => 'REQ-' . strtoupper(uniqid()),
            'status' => 'pending',
            'start_date' => Carbon::now()->addDay(),
            'end_date' => Carbon::now()->addDays(3),
            'total_amount' => 120,
            'final_amount' => 120,
            'duration_days' => 3,
            'unit_price' => 40
        ]);

        $response = $this->actingAs($this->prestataireUser)
            ->get(route('prestataire.bookings.index', ['type' => 'equipment']));

        $response->assertStatus(200)
            ->assertSee('Voir détails');
    }
}
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Prestataire;
use App\Models\Service;
use App\Models\Review;
use App\Models\Skill;
use App\Models\Message;
use App\Models\Booking;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PrestatairesShowViewTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $prestataire;
    protected $client;
    protected $clientUser;
    protected $prestataireUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur prestataire
        $this->prestataireUser = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'prestataire@test.com',
            'role' => 'prestataire'
        ]);
        
        // Créer un prestataire
        $this->prestataire = Prestataire::factory()->create([
            'user_id' => $this->prestataireUser->id,
            'secteur_activite' => 'Développement Web',
            'description' => 'Expert en développement web avec 5 ans d\'expérience',
            'is_approved' => true
        ]);
        
        // Créer un utilisateur client
        $this->clientUser = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'client@test.com',
            'role' => 'client'
        ]);
        
        // Créer un client
        $this->client = Client::factory()->create([
            'user_id' => $this->clientUser->id
        ]);
    }

    /** @test */
    public function it_displays_prestataire_profile_page_successfully()
    {
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertStatus(200)
                 ->assertViewIs('prestataires.show')
                 ->assertViewHas('prestataire', $this->prestataire)
                 ->assertSee($this->prestataire->user->name)
                 ->assertSee($this->prestataire->secteur_activite)
                 ->assertSee($this->prestataire->description);
    }

    /** @test */
    public function it_displays_verified_badge_for_verified_prestataire()
    {
        $this->prestataire->update(['is_verified' => true]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Profil Vérifié');
    }

    /** @test */
    public function it_does_not_display_verified_badge_for_unverified_prestataire()
    {
        $this->prestataire->update(['is_approved' => false]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertDontSee('Profil Vérifié');
    }

    /** @test */
    public function it_displays_prestataire_photo_when_available()
    {
        Storage::fake('public');
        $photo = UploadedFile::fake()->image('prestataire.jpg');
        $photoPath = $photo->store('prestataires', 'public');
        
        $this->prestataire->update(['photo' => $photoPath]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('storage/' . $photoPath);
    }

    /** @test */
    public function it_displays_default_avatar_when_no_photo()
    {
        $this->prestataire->update(['photo' => null]);
        $this->prestataireUser->update(['avatar' => null, 'profile_photo_url' => null]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        // Vérifier que l'icône SVG par défaut est affichée
        $response->assertSee('viewBox="0 0 24 24"', false);
    }

    /** @test */
    public function it_displays_skills_when_available()
    {
        $skill1 = Skill::factory()->create(['name' => 'PHP']);
        $skill2 = Skill::factory()->create(['name' => 'Laravel']);
        
        $this->prestataire->skills()->attach([$skill1->id, $skill2->id]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Compétences')
                 ->assertSee('PHP')
                 ->assertSee('Laravel');
    }

    /** @test */
    public function it_does_not_display_skills_section_when_no_skills()
    {
        // S'assurer qu'il n'y a pas de compétences
        $this->prestataire->skills()->detach();
        $this->prestataire->refresh();
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertDontSee('Compétences');
    }

    /** @test */
    public function it_displays_services_with_correct_information()
    {
        $service = Service::factory()->create([
            'prestataire_id' => $this->prestataire->id,
            'title' => 'Développement Site Web',
            'description' => 'Création de sites web modernes',
            'price' => 1500.00
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Services proposés')
                 ->assertSee('Développement Site Web')
                 ->assertSee('Création de sites web modernes')
                 ->assertSee('1 500 €')
                 ->assertSee('Voir détails');
    }

    /** @test */
    public function it_displays_no_services_message_when_empty()
    {
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Aucun service disponible')
                 ->assertSee('Ce prestataire n\'a pas encore publié de services.', false);
    }

    /** @test */
    public function it_displays_reviews_with_ratings()
    {
        $review = Review::factory()->create([
            'prestataire_id' => $this->prestataire->id,
            'client_id' => $this->client->id,
            'rating' => 5,
            'comment' => 'Excellent travail, très professionnel!'
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Avis clients')
                 ->assertSee('Excellent travail, très professionnel!')
                 ->assertSee('5.0')
                 ->assertSee('1 avis');
    }

    /** @test */
    public function it_displays_no_reviews_message_when_empty()
    {
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Aucun avis')
                 ->assertSee('Soyez le premier à laisser un avis!');
    }

    /** @test */
    public function it_shows_follow_button_for_authenticated_client()
    {
        $this->actingAs($this->clientUser);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('S\'abonner', false);
    }

    /** @test */
    public function it_shows_unfollow_button_when_client_is_following()
    {
        $this->actingAs($this->clientUser);
        $this->client->followedPrestataires()->attach($this->prestataire->id);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Abonné(e)');
    }

    /** @test */
    public function it_shows_contact_button_for_authenticated_client()
    {
        $this->actingAs($this->clientUser);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Contacter');
    }

    /** @test */
    public function it_does_not_show_action_buttons_for_unauthenticated_users()
    {
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertDontSee('S\'abonner')
                 ->assertDontSee('Contacter');
    }

    /** @test */
    public function it_shows_review_form_button_for_clients_with_interactions()
    {
        $this->actingAs($this->clientUser);
        
        // Créer une interaction (message)
        Message::factory()->create([
            'sender_id' => $this->clientUser->id,
            'receiver_id' => $this->prestataireUser->id,
            'content' => 'Bonjour, je suis intéressé par vos services'
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Laisser un avis');
    }

    /** @test */
    public function it_shows_review_form_button_for_clients_with_bookings()
    {
        $this->actingAs($this->clientUser);
        
        // Créer une réservation
        Booking::factory()->create([
            'client_id' => $this->client->id,
            'prestataire_id' => $this->prestataire->id,
            'status' => 'confirmed'
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Laisser un avis');
    }

    /** @test */
    public function it_does_not_show_review_form_for_clients_without_interactions()
    {
        $this->actingAs($this->clientUser);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertDontSee('Laisser un avis');
    }

    /** @test */
    public function it_does_not_show_review_form_if_client_already_reviewed()
    {
        $this->actingAs($this->clientUser);
        
        // Créer une interaction
        Message::factory()->create([
            'sender_id' => $this->clientUser->id,
            'receiver_id' => $this->prestataireUser->id,
            'content' => 'Test message'
        ]);
        
        // Créer un avis existant
        $review = Review::factory()->create([
            'prestataire_id' => $this->prestataire->id,
            'client_id' => $this->clientUser->id, 
            'rating' => 4,
            'comment' => 'Bon travail'
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertDontSee('Laisser un avis');
    }

    /** @test */
    public function it_contains_javascript_for_review_form_interactions()
    {
        $this->actingAs($this->clientUser);
        
        Message::factory()->create([
            'sender_id' => $this->clientUser->id,
            'receiver_id' => $this->prestataireUser->id,
            'content' => 'Test message'
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('show-review-form')
                 ->assertSee('hide-review-form')
                 ->assertSee('addEventListener');
    }

    /** @test */
    public function it_contains_star_rating_javascript()
    {
        $this->actingAs($this->clientUser);
        
        Message::factory()->create([
            'sender_id' => $this->clientUser->id,
            'receiver_id' => $this->prestataireUser->id,
            'content' => 'Test message'
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('star-rating')
                 ->assertSee('rating-input')
                 ->assertSee('<svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">', false)
                 ->assertSee('data-rating="1"', false)
                 ->assertSee('data-rating="5"', false);
    }

    /** @test */
    public function it_displays_correct_average_rating()
    {
        // Créer plusieurs avis avec différentes notes
        Review::factory()->create([
            'prestataire_id' => $this->prestataire->id,
            'client_id' => $this->client->id,
            'rating' => 5
        ]);
        
        $client2 = Client::factory()->create();
        Review::factory()->create([
            'prestataire_id' => $this->prestataire->id,
            'client_id' => $client2->id,
            'rating' => 3
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('4.0'); // Moyenne de 5 et 3
    }

    /** @test */
    public function it_displays_service_count_badge()
    {
        Service::factory()->count(3)->create([
            'prestataire_id' => $this->prestataire->id
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('3'); // Badge avec le nombre de services
    }

    /** @test */
    public function it_handles_missing_prestataire_gracefully()
    {
        $response = $this->get('/prestataires/999');
        
        $response->assertStatus(404);
    }

    /** @test */
    public function it_displays_contact_information_section()
    {
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Informations de contact');
    }

    /** @test */
    public function it_displays_equipment_section_when_available()
    {
        // Note: Cette fonctionnalité semble être dans la vue mais pas encore implémentée
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('Équipements disponibles à la location');
    }

    /** @test */
    public function review_form_contains_required_fields()
    {
        $this->actingAs($this->clientUser);
        
        Message::factory()->create([
            'sender_id' => $this->clientUser->id,
            'receiver_id' => $this->prestataireUser->id,
            'content' => 'Test message'
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('name="rating"', false)
                 ->assertSee('name="comment"', false)
                 ->assertSee('csrf');
    }

    /** @test */
    public function it_displays_character_counter_for_comment_field()
    {
        $this->actingAs($this->clientUser);
        
        Message::factory()->create([
            'sender_id' => $this->clientUser->id,
            'receiver_id' => $this->prestataireUser->id,
            'content' => 'Test message'
        ]);
        
        $response = $this->get(route('prestataires.show', $this->prestataire));
        
        $response->assertSee('character-count')
                 ->assertSee('500'); // Limite de caractères
    }
}
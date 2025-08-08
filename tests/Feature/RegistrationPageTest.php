<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationPageTest extends TestCase
{
    /** @test */
    public function test_page_inscription_se_charge_correctement()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('Inscription');
        $response->assertSee('Client');
        $response->assertSee('Prestataire');
    }

    /** @test */
    public function test_formulaire_inscription_contient_champs_requis()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        
        // Vérifier la présence des champs de base
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="password_confirmation"', false);
        $response->assertSee('name="user_type"', false);
    }

    /** @test */
    public function test_formulaire_client_contient_champs_specifiques()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        
        // Vérifier la présence des champs spécifiques au client
        $response->assertSee('name="location"', false);
        $response->assertSee('name="client_profile_photo"', false);
    }

    /** @test */
    public function test_formulaire_prestataire_contient_champs_specifiques()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        
        // Vérifier la présence des champs spécifiques au prestataire
        $response->assertSee('name="company_name"', false);
        $response->assertSee('name="phone"', false);
        $response->assertSee('name="city"', false);
        $response->assertSee('name="prestataire_profile_photo"', false);
        $response->assertSee('name="category_id"', false);
        $response->assertSee('name="subcategory_id"', false);
    }

    /** @test */
    public function test_boutons_inscription_sont_presents()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        
        // Vérifier la présence des boutons d'inscription
        $response->assertSee('type="submit"', false);
    }

    /** @test */
    public function test_formulaire_utilise_methode_post()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        
        // Vérifier que le formulaire utilise la méthode POST
        $response->assertSee('method="POST"', false);
    }

    /** @test */
    public function test_formulaire_pointe_vers_route_register()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        
        // Vérifier que le formulaire contient l'action vers la route d'inscription
        $response->assertSee('action=', false);
        $response->assertSee('register', false);
    }

    /** @test */
    public function test_javascript_categories_est_present()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        
        // Vérifier la présence du JavaScript pour les catégories
        $response->assertSee('loadMainCategories', false);
        $response->assertSee('loadSubcategories', false);
    }

    /** @test */
    public function test_css_styles_sont_appliques()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        
        // Vérifier la présence des styles CSS
        $response->assertSee('.form-control', false);
        $response->assertSee('.form-label', false);
    }

    /** @test */
    public function test_route_post_register_existe()
    {
        // Test simple pour vérifier que la route POST existe
        // (sans envoyer de données pour éviter les erreurs de validation)
        $response = $this->post('/register', []);
        
        // La route doit exister (pas d'erreur 404)
        // Même si elle retourne des erreurs de validation, c'est normal
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function test_endpoint_categories_main_existe()
    {
        $response = $this->get('/categories/main');
        
        // L'endpoint doit exister (pas d'erreur 404)
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function test_page_inscription_contient_titre()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('<title>', false);
    }
}
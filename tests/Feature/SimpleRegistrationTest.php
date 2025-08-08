<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class SimpleRegistrationTest extends TestCase
{
    use WithoutMiddleware;

    /** @test */
    public function test_registration_page_loads()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function test_registration_page_contains_forms()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('S\'inscrire en tant que Client');
        $response->assertSee('S\'inscrire en tant que Prestataire');
    }

    /** @test */
    public function test_client_form_has_required_fields()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="phone"', false);
    }

    /** @test */
    public function test_prestataire_form_has_required_fields()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('name="category_id"', false);
        $response->assertSee('name="description"', false);
    }

    /** @test */
    public function test_categories_endpoint_exists()
    {
        $response = $this->get('/categories/main');
        
        $response->assertStatus(200);
    }
}
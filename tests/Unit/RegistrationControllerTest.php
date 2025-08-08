<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Validator;

class RegistrationControllerTest extends TestCase
{
    use WithoutMiddleware;

    /** @test */
    public function test_page_inscription_se_charge()
    {
        $controller = new RegisterController();
        $request = new Request();
        
        $response = $controller->showRegistrationForm($request);
        
        $this->assertNotNull($response);
        $this->assertEquals('auth.register', $response->getName());
    }

    /** @test */
    public function test_validation_nom_requis()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    /** @test */
    public function test_validation_email_requis()
    {
        $data = [
            'name' => 'Test User',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /** @test */
    public function test_validation_mot_de_passe_requis()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type' => 'client',
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /** @test */
    public function test_validation_type_utilisateur_requis()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('user_type'));
    }

    /** @test */
    public function test_validation_mot_de_passe_confirmation()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'DifferentPassword',
            'user_type' => 'client',
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /** @test */
    public function test_validation_mot_de_passe_complexite()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'simple',
            'password_confirmation' => 'simple',
            'user_type' => 'client',
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /** @test */
    public function test_validation_donnees_valides_client()
    {
        $data = [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_validation_nom_minimum_caracteres()
    {
        $data = [
            'name' => 'A', // Moins de 2 caractères
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    /** @test */
    public function test_validation_email_format()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'email-invalide', // Format invalide
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'client',
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /** @test */
    public function test_validation_type_utilisateur_valide()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'user_type' => 'invalide', // Type non autorisé
        ];

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'user_type' => ['required', 'in:client,prestataire'],
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('user_type'));
    }
}
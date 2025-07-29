<?php

namespace Database\Factories;

use App\Models\Prestataire;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prestataire>
 */
class PrestataireFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Prestataire::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_name' => fake()->company(),
            'description' => fake()->paragraph(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => 'France',
            'years_experience' => fake()->numberBetween(1, 20),

            'background_check_status' => 'approved',
            'is_approved' => true,
            'is_active' => true,
            'requires_approval' => false,
            'min_advance_hours' => 0,
            'max_advance_days' => 30,
            'buffer_between_appointments' => 0,
        ];
    }

    /**
     * Indicate that the prestataire is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
            
            'background_check_status' => 'approved',
        ]);
    }

    /**
     * Indicate that the prestataire is not approved.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
            
            'background_check_status' => 'pending',
        ]);
    }



    /**
     * Indicate that the prestataire is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
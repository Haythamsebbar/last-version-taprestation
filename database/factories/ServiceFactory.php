<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Prestataire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prestataire_id' => Prestataire::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 50, 500),
            'delivery_time' => fake()->randomElement([
                '1-2 jours',
                '3-5 jours',
                '1 semaine',
                '2 semaines',
                '1 mois'
            ]),
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the service is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the service is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Create a service with a specific price range.
     */
    public function priceRange(float $min, float $max): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, $min, $max),
        ]);
    }

    /**
     * Create a service with a specific delivery time.
     */
    public function deliveryTime(string $time): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_time' => $time,
        ]);
    }
}
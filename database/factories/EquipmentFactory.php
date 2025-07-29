<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\Prestataire;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EquipmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Equipment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $name = $this->faker->words(3, true),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence,
            'price_per_day' => $this->faker->randomFloat(2, 10, 1000),
            'prestataire_id' => Prestataire::factory(),
            'city' => $this->faker->city,
        ];
    }
}
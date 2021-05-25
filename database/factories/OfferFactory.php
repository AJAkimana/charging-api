<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Offer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['LG', 'Samsung', 'iPhone', 'Techno']),
            'amount' => rand(100, 900) * 1000,
            'currency' => $this->faker->randomElement(['RwF']),
            'required_score' => rand(35, 100),
        ];
    }
}

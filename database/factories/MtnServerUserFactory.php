<?php

namespace Database\Factories;

use App\Models\MtnServerUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class MtnServerUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MtnServerUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'names' => $this->faker->name(),
            'phoneNumber' => $this->faker->unique()->phoneNumber,
            'location' => $this->faker->randomElement(['Kigali', 'Musanze', 'Rusizi', 'Akagera']),
            'dateOfBirth' => $this->faker->dateTimeBetween('-50 years', '-20 years'),
            'kyc' => $this->faker->unique()->slug(3),
        ];
    }
}

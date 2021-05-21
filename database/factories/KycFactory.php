<?php

namespace Database\Factories;

use App\Models\Kyc;
use Illuminate\Database\Eloquent\Factories\Factory;

class KycFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Kyc::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'phoneNumber' => $this->faker->unique()->phoneNumber,
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'dob' => $this->faker->dateTimeBetween('-50 years', '-20 years'),
            'idType' => $this->faker->randomElement(['ID', 'Passport']),
            'idNumber' => rand(1000000000, 9999999999),
            'language' => $this->faker->randomElement(['Kinyarwanda', 'Francais', 'English']),
            'address' => $this->faker->randomElement(['Kigali', 'Musanze', 'Rusizi', 'Akagera']),
        ];
    }
}

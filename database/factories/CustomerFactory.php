<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'names' => $this->faker->name(),
            'msisdn' => $this->faker->unique()->phoneNumber,
            'age' => rand(37, 80),
            'location' => $this->faker->randomElement(['Kigali', 'Musanze', 'Nyamasheke', 'Akagera']),
            'kyc' => $this->faker->unique()->slug(3),
        ];
    }
}


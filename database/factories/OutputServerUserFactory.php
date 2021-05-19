<?php

namespace Database\Factories;

use App\Models\OutputServerUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OutputServerUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OutputServerUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'msisdn' => $this->faker->unique()->phoneNumber,
            'age' => rand(17, 80),
        ];
    }
}

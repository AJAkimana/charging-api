<?php

namespace Database\Factories;

use App\Models\LoanAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount' => $this->faker->randomFloat(0, 100, 10000),
        ];
    }
}

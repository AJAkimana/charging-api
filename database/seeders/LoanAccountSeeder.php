<?php

namespace Database\Seeders;

use App\Models\LoanAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer = User::factory()->create();
        LoanAccount::factory()->count(1)->for($customer)->create();
    }
}

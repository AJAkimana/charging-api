<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\LoanAccount;
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
        $customer = Customer::factory()->create();
        LoanAccount::factory()->count(1)->for($customer)->create();
    }
}

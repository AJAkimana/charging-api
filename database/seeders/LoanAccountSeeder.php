<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Kyc;
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
        $kyc = Kyc::factory()->create();
        $customer = Customer::factory()->for($kyc)->create();
        LoanAccount::factory()->count(1)->for($customer)->create();
    }
}

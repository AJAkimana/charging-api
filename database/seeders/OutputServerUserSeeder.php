<?php

namespace Database\Seeders;

use App\Models\OutputServerUser;
use Illuminate\Database\Seeder;

class OutputServerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OutputServerUser::factory()->count(50)->create();
    }
}

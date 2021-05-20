<?php

namespace Database\Seeders;

use App\Models\MtnServerUser;
use Illuminate\Database\Seeder;

class MtnServerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MtnServerUser::factory()->count(50)->create();
    }
}

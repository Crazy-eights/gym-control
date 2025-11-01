<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisualConfig;

class VisualConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VisualConfig::seedDefaults();
    }
}

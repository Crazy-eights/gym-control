<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            'site_name' => 'Gym Control',
            'site_description' => 'Sistema de gestión integral para gimnasios',
            'site_email' => 'admin@gymcontrol.com',
            'site_phone' => '',
            'site_address' => '',
            'currency' => 'USD',
            'timezone' => 'America/Mexico_City',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }
    }
}
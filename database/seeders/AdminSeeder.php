<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un admin de prueba
        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'firstname' => 'Administrador',
            'lastname' => 'Principal',
            'email' => 'admin@gymcontrol.com',
            'photo' => '',
            'created_on' => date('Y-m-d'),
        ]);
    }
}
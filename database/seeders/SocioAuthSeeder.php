<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class SocioAuthSeeder extends Seeder
{
    public function run()
    {
        // Obtener el primer socio
        $socio = Member::first();
        
        if ($socio) {
            // Asignar credenciales de acceso (sin usar mutador)
            $socio->email = 'socio@gym.com';
            $socio->password = Hash::make('123456');
            $socio->save();
            
            echo "✅ Credenciales creadas para: {$socio->firstname} {$socio->lastname}\n";
            echo "📧 Email: socio@gym.com\n";
            echo "🔑 Password: 123456\n";
        } else {
            echo "❌ No se encontraron socios en la base de datos.\n";
        }
        
        // Crear otro socio de ejemplo si hay más
        $segundoSocio = Member::skip(1)->first();
        if ($segundoSocio) {
            $segundoSocio->email = 'maria@gym.com';
            $segundoSocio->password = Hash::make('123456');
            $segundoSocio->save();
            
            echo "✅ Credenciales creadas para: {$segundoSocio->firstname} {$segundoSocio->lastname}\n";
            echo "📧 Email: maria@gym.com\n";
            echo "🔑 Password: 123456\n";
        }
    }
}
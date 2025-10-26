<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class CheckAdminPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:check-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check admin passwords and optionally set them';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $admins = Admin::all(['id', 'username', 'email', 'password']);
        
        $this->info('=== VERIFICACIÓN DE CONTRASEÑAS DE ADMIN ===');
        $this->newLine();
        
        foreach ($admins as $admin) {
            $hasPassword = !empty($admin->password);
            $passwordLength = strlen($admin->password ?? '');
            
            $this->info("Admin ID: {$admin->id}");
            $this->info("Username: {$admin->username}");
            $this->info("Email: {$admin->email}");
            $this->info("Tiene contraseña: " . ($hasPassword ? 'SÍ' : 'NO'));
            $this->info("Longitud password: {$passwordLength}");
            
            if (!$hasPassword) {
                $this->warn("⚠️  Este admin NO tiene contraseña!");
                
                if ($this->confirm("¿Quieres establecer la contraseña 'admin123' para {$admin->username}?")) {
                    $admin->password = Hash::make('admin123');
                    $admin->save();
                    $this->info("✅ Contraseña establecida para {$admin->username}");
                }
            } else {
                // Verificar si la contraseña actual funciona
                if (Hash::check('admin123', $admin->password)) {
                    $this->info("✅ La contraseña 'admin123' funciona para {$admin->username}");
                } elseif (Hash::check('admin', $admin->password)) {
                    $this->info("✅ La contraseña 'admin' funciona para {$admin->username}");
                } elseif (Hash::check('123456', $admin->password)) {
                    $this->info("✅ La contraseña '123456' funciona para {$admin->username}");
                } else {
                    $this->warn("⚠️  No se pudo verificar la contraseña actual");
                    if ($this->confirm("¿Quieres resetear la contraseña a 'admin123' para {$admin->username}?")) {
                        $admin->password = Hash::make('admin123');
                        $admin->save();
                        $this->info("✅ Contraseña reseteada para {$admin->username}");
                    }
                }
            }
            
            $this->newLine();
        }
        
        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\Member;
use App\Notifications\MembershipExpiring;
use App\Notifications\NewMemberRegistered;
use App\Notifications\PaymentReceived;

class GenerateTestNotifications extends Command
{
    protected $signature = 'notifications:generate-test';
    protected $description = 'Genera notificaciones de prueba para el sistema';

    public function handle()
    {
        $admin = Admin::first();

        if (!$admin) {
            $this->error('No se encontró ningún administrador');
            return 1;
        }

        $member = Member::first();

        if (!$member) {
            $this->error('No se encontró ningún socio');
            return 1;
        }

        // Notificación de membresía por vencer
        $admin->notify(new MembershipExpiring($member, 7));
        $this->info('✓ Notificación de membresía por vencer creada');

        // Notificación de nuevo socio
        $admin->notify(new NewMemberRegistered($member));
        $this->info('✓ Notificación de nuevo socio creada');

        // Notificación de pago recibido
        $admin->notify(new PaymentReceived($member, 1500));
        $this->info('✓ Notificación de pago recibido creada');

        $this->info('');
        $this->info('🎉 Se generaron 3 notificaciones de prueba correctamente');

        return 0;
    }
}

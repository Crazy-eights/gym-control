<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\Admin;
use App\Notifications\MembershipExpiring;
use Carbon\Carbon;

class CheckExpiringMemberships extends Command
{
    protected $signature = 'memberships:check-expiring';
    protected $description = 'Verifica membresías por vencer y envía notificaciones';

    public function handle()
    {
        $this->info('Verificando membresías vencidas y por vencer...');

        // Buscar socios con membresías vencidas (hasta 30 días atrás) o por vencer (próximos 7 días)
        $expiringMembers = Member::whereBetween('subscription_end_date', [
            now()->subDays(30)->startOfDay(), // Membresías vencidas en los últimos 30 días
            now()->addDays(7)->endOfDay()      // Membresías que vencen en los próximos 7 días
        ])
        ->whereNotNull('subscription_end_date')
        ->whereNotNull('plan_id')
        ->get();

        if ($expiringMembers->isEmpty()) {
            $this->info('No hay membresías vencidas o por vencer.');
            return 0;
        }

        $admins = Admin::all();
        $notificationsSent = 0;

        foreach ($expiringMembers as $member) {
            $endDate = Carbon::parse($member->subscription_end_date);
            $daysLeft = now()->diffInDays($endDate, false);
            
            foreach ($admins as $admin) {
                $admin->notify(new MembershipExpiring($member, (int)$daysLeft));
                $notificationsSent++;
            }
            
            if ($daysLeft < 0) {
                $this->line("✓ Notificación enviada para {$member->firstname} {$member->lastname} (vencida hace " . abs($daysLeft) . " días)");
            } else {
                $this->line("✓ Notificación enviada para {$member->firstname} {$member->lastname} ({$daysLeft} días restantes)");
            }
        }

        $this->info('');
        $this->info("✅ Proceso completado: {$notificationsSent} notificaciones enviadas para {$expiringMembers->count()} socios.");

        return 0;
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\MemberAttendance;
use Carbon\Carbon;

class MemberAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los miembros
        $members = Member::all();

        if ($members->isEmpty()) {
            $this->command->warn('No hay miembros en la base de datos. Ejecuta el seeder de miembros primero.');
            return;
        }

        $this->command->info('Generando datos de asistencia para ' . $members->count() . ' miembros...');

        // Generar asistencias para los últimos 30 días
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $currentDate = $startDate->copy();
        $totalAttendances = 0;

        while ($currentDate <= $endDate) {
            // Solo crear asistencias para días de la semana (lunes a viernes)
            if ($currentDate->isWeekday()) {
                // Número aleatorio de asistencias por día (entre 5 y 15)
                $dailyAttendances = rand(5, 15);

                // Seleccionar miembros aleatorios para ese día
                $dailyMembers = $members->random(min($dailyAttendances, $members->count()));

                foreach ($dailyMembers as $member) {
                    // Horario aleatorio entre 6:00 AM y 10:00 PM
                    $randomHour = rand(6, 22);
                    $randomMinute = rand(0, 59);

                    $checkinTime = $currentDate->copy()->setTime($randomHour, $randomMinute);

                    MemberAttendance::create([
                        'member_id' => $member->id,
                        'checkin_time' => $checkinTime,
                        'attendance_date' => $currentDate->toDateString()
                    ]);

                    $totalAttendances++;
                }
            }

            $currentDate->addDay();
        }

        $this->command->info("✅ Se crearon {$totalAttendances} registros de asistencia.");

        // Crear algunas asistencias para hoy
        $todayAttendances = rand(8, 12);
        $todayMembers = $members->random(min($todayAttendances, $members->count()));

        foreach ($todayMembers as $member) {
            $randomHour = rand(6, 20);
            $randomMinute = rand(0, 59);

            $checkinTime = Carbon::today()->setTime($randomHour, $randomMinute);

            MemberAttendance::create([
                'member_id' => $member->id,
                'checkin_time' => $checkinTime,
                'attendance_date' => Carbon::today()->toDateString()
            ]);
        }

        $this->command->info("✅ Se crearon {$todayAttendances} asistencias para hoy.");
        $this->command->info("📊 Total de asistencias creadas: " . ($totalAttendances + $todayAttendances));
    }
}

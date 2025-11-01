<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymClass;
use App\Models\ClassSchedule;

class GymClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Creando clases de ejemplo...');

        $classes = [
            [
                'name' => 'Yoga Matutino',
                'description' => 'Clase de yoga para comenzar el día con energía y flexibilidad.',
                'instructor_name' => 'María González',
                'duration_minutes' => 60,
                'max_participants' => 15,
                'price' => 25.00,
                'difficulty_level' => 'principiante',
                'active' => true,
                'schedules' => [
                    ['day_of_week' => 'lunes', 'start_time' => '07:00'],
                    ['day_of_week' => 'miercoles', 'start_time' => '07:00'],
                    ['day_of_week' => 'viernes', 'start_time' => '07:00'],
                ]
            ],
            [
                'name' => 'CrossFit Intensivo',
                'description' => 'Entrenamiento funcional de alta intensidad.',
                'instructor_name' => 'Carlos Ruiz',
                'duration_minutes' => 45,
                'max_participants' => 12,
                'price' => 35.00,
                'difficulty_level' => 'avanzado',
                'active' => true,
                'schedules' => [
                    ['day_of_week' => 'martes', 'start_time' => '18:00'],
                    ['day_of_week' => 'jueves', 'start_time' => '18:00'],
                ]
            ],
            [
                'name' => 'Pilates',
                'description' => 'Fortalecimiento del core y mejora de la postura.',
                'instructor_name' => 'Ana Martínez',
                'duration_minutes' => 50,
                'max_participants' => 10,
                'price' => 30.00,
                'difficulty_level' => 'intermedio',
                'active' => true,
                'schedules' => [
                    ['day_of_week' => 'lunes', 'start_time' => '19:00'],
                    ['day_of_week' => 'miercoles', 'start_time' => '19:00'],
                ]
            ],
            [
                'name' => 'Spinning',
                'description' => 'Cardio intenso en bicicleta estática con música motivadora.',
                'instructor_name' => 'Roberto Silva',
                'duration_minutes' => 40,
                'max_participants' => 20,
                'price' => 20.00,
                'difficulty_level' => 'intermedio',
                'active' => true,
                'schedules' => [
                    ['day_of_week' => 'martes', 'start_time' => '17:00'],
                    ['day_of_week' => 'jueves', 'start_time' => '17:00'],
                    ['day_of_week' => 'sabado', 'start_time' => '09:00'],
                ]
            ],
            [
                'name' => 'Zumba',
                'description' => 'Baile fitness con ritmos latinos.',
                'instructor_name' => 'Sofia López',
                'duration_minutes' => 60,
                'max_participants' => 25,
                'price' => 18.00,
                'difficulty_level' => 'principiante',
                'active' => true,
                'schedules' => [
                    ['day_of_week' => 'lunes', 'start_time' => '20:00'],
                    ['day_of_week' => 'miercoles', 'start_time' => '20:00'],
                    ['day_of_week' => 'viernes', 'start_time' => '20:00'],
                ]
            ]
        ];

        foreach ($classes as $classData) {
            $schedules = $classData['schedules'];
            unset($classData['schedules']);

            $gymClass = GymClass::create($classData);

            // Crear horarios para cada clase
            foreach ($schedules as $schedule) {
                ClassSchedule::create([
                    'gym_class_id' => $gymClass->id,
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => date('H:i', strtotime($schedule['start_time'] . ' +' . $gymClass->duration_minutes . ' minutes')),
                    'is_recurring' => true,
                    'active' => true
                ]);
            }

            $this->command->info("   ✅ Clase creada: {$gymClass->name}");
        }

        $this->command->info("🎉 Se crearon " . count($classes) . " clases de ejemplo con sus horarios.");
    }
}

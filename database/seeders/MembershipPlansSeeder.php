<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipPlan;

class MembershipPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'plan_name' => 'Pase Diario',
                'description' => 'Acceso completo al gimnasio por un día. Ideal para visitantes ocasionales. Incluye acceso a todas las máquinas, pesas libres y área de cardio.',
                'price' => 15.00,
                'duration_days' => 1,
            ],
            [
                'plan_name' => 'Membresía Semanal',
                'description' => 'Acceso completo por una semana. Perfecto para probar nuestras instalaciones. Incluye todas las áreas del gimnasio y clases grupales básicas.',
                'price' => 80.00,
                'duration_days' => 7,
            ],
            [
                'plan_name' => 'Plan Básico Mensual',
                'description' => 'Membresía mensual con acceso a todas las máquinas y área de pesas. Incluye acceso a vestuarios, duchas y área de cardio durante horarios regulares.',
                'price' => 250.00,
                'duration_days' => 30,
            ],
            [
                'plan_name' => 'Plan Premium Mensual',
                'description' => 'Membresía premium con acceso 24/7, todas las clases grupales, asesoría nutricional básica, y descuentos en productos de la tienda del gimnasio.',
                'price' => 450.00,
                'duration_days' => 30,
            ],
            [
                'plan_name' => 'Plan VIP Mensual',
                'description' => 'Membresía VIP con entrenador personal (2 sesiones/mes), acceso a zona VIP, clases premium, toallas incluidas, y bebidas energéticas gratuitas.',
                'price' => 750.00,
                'duration_days' => 30,
            ],
            [
                'plan_name' => 'Plan Estudiantil',
                'description' => 'Membresía especial para estudiantes con descuento. Acceso completo durante horarios de estudio (6 AM - 4 PM) de lunes a viernes. Fines de semana acceso completo.',
                'price' => 180.00,
                'duration_days' => 30,
            ],
            [
                'plan_name' => 'Plan Trimestral',
                'description' => 'Membresía por 3 meses con descuento especial. Acceso completo, clases grupales, plan nutricional básico y evaluación física inicial y final.',
                'price' => 650.00,
                'duration_days' => 90,
            ],
            [
                'plan_name' => 'Plan Anual Premium',
                'description' => 'Membresía anual con máximo descuento. Incluye todo: acceso 24/7, entrenador personal (1 sesión/semana), clases premium, evaluaciones médicas trimestrales, y nutriólogo personal.',
                'price' => 2400.00,
                'duration_days' => 365,
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::create($plan);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberAttendance;
use App\Models\MembershipPlan;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal del admin
     */
    public function index()
    {
        try {
            // Limpiar caché si hay problemas de serialización
            $this->clearCorruptedCache();
            
            $data = $this->getDashboardData();
            
            return view('admin.dashboard-modern', $data);
        } catch (\Exception $e) {
            Log::error('Error en dashboard admin: ' . $e->getMessage());
            
            // Si hay error, limpiar toda la caché y intentar con datos básicos
            Cache::flush();
            session()->flush();
            
            return view('admin.dashboard-modern', $this->getBasicDashboardData());
        }
    }

    /**
     * Obtiene los datos principales del dashboard
     */
    private function getDashboardData()
    {
        // Cachear datos por 5 minutos para mejorar rendimiento
        return Cache::remember('admin.dashboard.data', 300, function () {
            return [
                'totalMembers' => Member::count(),
                'activeMembers' => Member::where('status', 'active')->count(),
                'todayAttendances' => $this->getTodayAttendances(),
                'monthlyRevenue' => $this->getMonthlyRevenue(),
                'membershipPlans' => MembershipPlan::with('members')->get(),
                'recentMembers' => Member::latest()->take(5)->get(),
                'attendanceStats' => $this->getAttendanceStats(),
                'planStats' => $this->getPlanStats()
            ];
        });
    }

    /**
     * Obtiene datos básicos en caso de error
     */
    private function getBasicDashboardData()
    {
        try {
            return [
                'totalMembers' => Member::count() ?? 0,
                'activeMembers' => Member::where('status', 'active')->count() ?? 0,
                'todayAttendances' => 0,
                'monthlyRevenue' => 0,
                'membershipPlans' => collect([]),
                'recentMembers' => collect([]),
                'attendanceStats' => [],
                'planStats' => []
            ];
        } catch (\Exception $e) {
            // Si incluso esto falla, devolver datos vacíos
            return [
                'totalMembers' => 0,
                'activeMembers' => 0,
                'todayAttendances' => 0,
                'monthlyRevenue' => 0,
                'membershipPlans' => collect([]),
                'recentMembers' => collect([]),
                'attendanceStats' => [],
                'planStats' => []
            ];
        }
    }

    /**
     * Obtiene asistencias de hoy
     */
    private function getTodayAttendances()
    {
        try {
            return MemberAttendance::whereDate('created_at', Carbon::today())->count();
        } catch (\Exception $e) {
            Log::warning('Error obteniendo asistencias de hoy: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene ingresos del mes actual
     */
    private function getMonthlyRevenue()
    {
        try {
            return Member::whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->join('membership_plans', 'members.membership_plan_id', '=', 'membership_plans.id')
                        ->sum('membership_plans.price') ?? 0;
        } catch (\Exception $e) {
            Log::warning('Error obteniendo ingresos mensuales: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene estadísticas de asistencias para gráficos
     */
    private function getAttendanceStats()
    {
        try {
            $stats = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $stats[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('D'),
                    'count' => MemberAttendance::whereDate('created_at', $date)->count()
                ];
            }
            return $stats;
        } catch (\Exception $e) {
            Log::warning('Error obteniendo estadísticas de asistencias: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene estadísticas de planes de membresía
     */
    private function getPlanStats()
    {
        try {
            return MembershipPlan::withCount('members')->get()->map(function ($plan) {
                return [
                    'name' => $plan->name,
                    'count' => $plan->members_count,
                    'price' => $plan->price
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::warning('Error obteniendo estadísticas de planes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Limpia caché corrupto
     */
    private function clearCorruptedCache()
    {
        try {
            // Intentar acceder al caché para detectar corrupción
            Cache::get('admin.dashboard.data');
        } catch (\Exception $e) {
            // Si hay error de serialización, limpiar caché relacionado
            Cache::forget('admin.dashboard.data');
            Cache::forget('members.count');
            Cache::forget('attendances.today');
            
            Log::info('Caché corrupto limpiado para dashboard admin');
        }
    }
}
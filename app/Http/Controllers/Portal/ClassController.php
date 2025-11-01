<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = GymClass::with(['schedules' => function($q) {
            $q->where('date', '>=', Carbon::today())
              ->orderBy('date')
              ->orderBy('start_time');
        }])
        ->where('is_active', true);

        // Filtros
        if ($request->filled('date')) {
            $query->whereHas('schedules', function($q) use ($request) {
                $q->where('date', $request->date);
            });
        }

        if ($request->filled('instructor')) {
            $query->where('instructor', $request->instructor);
        }

        if ($request->filled('duration')) {
            $query->where('duration', $request->duration);
        }

        $classes = $query->paginate(12);

        // Agregar próximos horarios para cada clase
        foreach ($classes as $class) {
            $class->upcomingSchedules = $class->schedules()
                ->with(['bookings' => function($q) {
                    $q->where('status', 'confirmed');
                }])
                ->where('date', '>=', Carbon::today())
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();
        }

        // Obtener lista de instructores para el filtro
        $instructors = GymClass::where('is_active', true)
                              ->distinct()
                              ->pluck('instructor')
                              ->filter()
                              ->sort()
                              ->values();

        return view('portal.classes.index', compact('classes', 'instructors'));
    }

    public function show(GymClass $class)
    {
        $class->load(['schedules' => function($q) {
            $q->orderBy('date')->orderBy('start_time');
        }]);

        // Obtener próximos horarios
        $upcomingSchedules = $class->schedules()
            ->with(['bookings' => function($q) {
                $q->where('status', 'confirmed');
            }])
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Próximo horario
        $nextSchedule = $upcomingSchedules->first();

        // Estadísticas
        $totalBookings = $class->schedules()
            ->withCount(['bookings' => function($q) {
                $q->where('status', 'confirmed');
            }])
            ->get()
            ->sum('bookings_count');

        // Promedio de ocupación
        $schedulesWithBookings = $class->schedules()
            ->with(['bookings' => function($q) {
                $q->where('status', 'confirmed');
            }])
            ->where('date', '<', Carbon::today())
            ->get();

        $averageOccupancy = 0;
        if ($schedulesWithBookings->count() > 0) {
            $totalOccupancy = $schedulesWithBookings->sum(function($schedule) use ($class) {
                $bookings = $schedule->bookings->where('status', 'confirmed')->count();
                return ($bookings / $class->max_capacity) * 100;
            });
            $averageOccupancy = $totalOccupancy / $schedulesWithBookings->count();
        }

        return view('portal.classes.show', compact(
            'class', 
            'upcomingSchedules', 
            'nextSchedule', 
            'totalBookings', 
            'averageOccupancy'
        ));
    }
}
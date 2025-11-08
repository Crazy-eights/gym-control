<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GymClassController extends Controller
{
    public function index()
    {
        $classes = GymClass::with(['schedules' => function($query) {
            $query->where('active', true);
        }])->orderBy('name')->paginate(10);

        // Calcular plazas disponibles
        $totalCapacity = $classes->sum('max_participants');
        $reservedSpots = 0;
        
        try {
            // Contar reservas activas para clases activas
            $reservedSpots = \App\Models\ClassBooking::join('class_schedules', 'class_bookings.class_schedule_id', '=', 'class_schedules.id')
                ->join('gym_classes', 'class_schedules.gym_class_id', '=', 'gym_classes.id')
                ->where('gym_classes.active', true)
                ->where('class_bookings.status', 'confirmed')
                ->whereDate('class_bookings.booking_date', '>=', today())
                ->count();
        } catch (\Exception $e) {
            $reservedSpots = 0;
        }
        
        $availableSpots = max(0, $totalCapacity - $reservedSpots);

        return view('admin.classes.index', compact('classes', 'totalCapacity', 'availableSpots'));
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructor_name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'max_participants' => 'required|integer|min:1|max:100',
            'price' => 'required|numeric|min:0',
            'difficulty_level' => 'required|in:principiante,intermedio,avanzado',
            'active' => 'nullable',
            // Validación para horarios
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required|integer|min:0|max:6',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.start_date' => 'nullable|date|after_or_equal:today',
            'schedules.*.end_date' => 'nullable|date|after:schedules.*.start_date',
            'schedules.*.is_recurring' => 'nullable',
            'schedules.*.active' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $gymClass = GymClass::create([
                'name' => $request->name,
                'description' => $request->description,
                'instructor_name' => $request->instructor_name,
                'duration_minutes' => $request->duration_minutes,
                'max_participants' => $request->max_participants,
                'price' => $request->price,
                'difficulty_level' => $request->difficulty_level,
                'active' => $request->has('active')
            ]);

            // Crear horarios si se proporcionaron
            if ($request->has('schedules') && is_array($request->schedules)) {
                foreach ($request->schedules as $scheduleData) {
                    if (!empty($scheduleData['day_of_week']) && !empty($scheduleData['start_time']) && !empty($scheduleData['end_time'])) {
                        $gymClass->schedules()->create([
                            'day_of_week' => $scheduleData['day_of_week'],
                            'start_time' => $scheduleData['start_time'],
                            'end_time' => $scheduleData['end_time'],
                            'start_date' => $scheduleData['start_date'] ?? now()->toDateString(),
                            'end_date' => $scheduleData['end_date'] ?? null,
                            'is_recurring' => isset($scheduleData['is_recurring']) ? true : false,
                            'active' => isset($scheduleData['active']) ? true : false
                        ]);
                    }
                }
            }

            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Clase creada exitosamente.',
                    'redirect' => route('admin.classes.index')
                ]);
            }

            return redirect()->route('admin.classes.index')
                           ->with('success', 'Clase creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la clase: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()
                        ->with('error', 'Error al crear la clase: ' . $e->getMessage());
        }
    }

    public function show(GymClass $class)
    {
        $class->load(['schedules', 'bookings.member']);
        
        $stats = [
            'total_bookings' => $class->bookings()->count(),
            'upcoming_sessions' => $class->schedules()->where('active', true)->count(),
            'revenue_this_month' => $class->bookings()
                ->where('booking_date', '>=', now()->startOfMonth())
                ->where('status', '!=', 'cancelled')
                ->count() * $class->price
        ];

        return view('admin.classes.show', compact('class', 'stats'));
    }

    public function edit(GymClass $class)
    {
        $class->load('schedules');
        return view('admin.classes.edit', compact('class'));
    }

    public function update(Request $request, GymClass $class)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructor_name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'max_participants' => 'required|integer|min:1|max:100',
            'price' => 'required|numeric|min:0',
            'difficulty_level' => 'required|in:principiante,intermedio,avanzado',
            'active' => 'nullable',
            // Validación para horarios
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required|integer|min:0|max:6',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.start_date' => 'nullable|date|after_or_equal:today',
            'schedules.*.end_date' => 'nullable|date|after:schedules.*.start_date',
            'schedules.*.is_recurring' => 'nullable',
            'schedules.*.active' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $class->update([
                'name' => $request->name,
                'description' => $request->description,
                'instructor_name' => $request->instructor_name,
                'duration_minutes' => $request->duration_minutes,
                'max_participants' => $request->max_participants,
                'price' => $request->price,
                'difficulty_level' => $request->difficulty_level,
                'active' => $request->has('active')
            ]);

            // Actualizar horarios si se proporcionaron
            if ($request->has('schedules') && is_array($request->schedules)) {
                // Eliminar horarios actuales que no tienen reservas futuras
                $class->schedules()->whereDoesntHave('bookings', function ($query) {
                    $query->where('booking_date', '>=', now()->toDateString())
                          ->where('status', 'confirmed');
                })->delete();

                // Crear nuevos horarios
                foreach ($request->schedules as $scheduleData) {
                    if (!empty($scheduleData['day_of_week']) && !empty($scheduleData['start_time']) && !empty($scheduleData['end_time'])) {
                        $class->schedules()->create([
                            'day_of_week' => $scheduleData['day_of_week'],
                            'start_time' => $scheduleData['start_time'],
                            'end_time' => $scheduleData['end_time'],
                            'start_date' => $scheduleData['start_date'] ?? now()->toDateString(),
                            'end_date' => $scheduleData['end_date'] ?? null,
                            'is_recurring' => isset($scheduleData['is_recurring']) ? true : false,
                            'active' => isset($scheduleData['active']) ? true : false
                        ]);
                    }
                }
            }

            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Clase actualizada exitosamente.',
                    'redirect' => route('admin.classes.index')
                ]);
            }

            return redirect()->route('admin.classes.index')
                           ->with('success', 'Clase actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la clase: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()
                        ->with('error', 'Error al actualizar la clase: ' . $e->getMessage());
        }
    }

    public function destroy(GymClass $class)
    {
        try {
            $futureBookings = $class->bookings()
                ->where('booking_date', '>=', now()->toDateString())
                ->where('status', 'confirmed')
                ->count();

            if ($futureBookings > 0) {
                return back()->with('error', 'No se puede eliminar la clase porque tiene reservas futuras confirmadas.');
            }

            $class->delete();
            return redirect()->route('admin.classes.index')
                           ->with('success', 'Clase eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la clase: ' . $e->getMessage());
        }
    }

    /**
     * Almacenar un nuevo horario para la clase
     */
    public function storeSchedule(Request $request, GymClass $class)
    {
        $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_recurring' => 'boolean',
            'active' => 'boolean'
        ], [
            'day_of_week.required' => 'Debe seleccionar un día de la semana.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        try {
            $class->schedules()->create([
                'day_of_week' => $request->day_of_week,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'start_date' => $request->start_date ?? now()->toDateString(),
                'end_date' => $request->end_date,
                'is_recurring' => $request->has('is_recurring'),
                'active' => $request->has('active')
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Horario agregado exitosamente.'
                ]);
            }

            return redirect()->back()->with('success', 'Horario agregado exitosamente.');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al agregar el horario: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al agregar el horario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un horario de la clase
     */
    public function destroySchedule(GymClass $class, ClassSchedule $schedule)
    {
        try {
            // Verificar que el horario pertenece a la clase
            if ($schedule->gym_class_id !== $class->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'El horario no pertenece a esta clase.'
                ], 403);
            }

            // Verificar si hay reservas futuras
            $futureBookings = $schedule->bookings()
                ->where('booking_date', '>=', now()->toDateString())
                ->where('status', 'confirmed')
                ->count();

            if ($futureBookings > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el horario porque tiene reservas futuras confirmadas.'
                ]);
            }

            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Horario eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el horario: ' . $e->getMessage()
            ], 500);
        }
    }
}

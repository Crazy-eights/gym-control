<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\GymClass;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
{
    public function index()
    {
        $schedules = ClassSchedule::with('gymClass')->orderBy('day_of_week')->orderBy('start_time')->paginate(20);
        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $gymClasses = GymClass::where('active', true)->orderBy('name')->get();
        return view('admin.schedules.create', compact('gymClasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gym_class_id' => 'required|exists:gym_classes,id',
            'day_of_week' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_recurring' => 'boolean',
            'active' => 'boolean'
        ]);

        // Verificar que no haya conflictos de horario
        $conflict = ClassSchedule::where('day_of_week', $request->day_of_week)
            ->where('active', true)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                          ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['time_conflict' => 'Ya existe un horario que se superpone con el horario seleccionado.'])
                        ->withInput();
        }

        $schedule = ClassSchedule::create([
            'gym_class_id' => $request->gym_class_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_recurring' => $request->has('is_recurring') ? true : false,
            'active' => $request->has('active') ? true : false
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Horario creado exitosamente.',
                'schedule' => $schedule
            ]);
        }

        return redirect()->route('admin.classes.show', $request->gym_class_id)
                       ->with('success', 'Horario agregado exitosamente.');
    }

    public function show(ClassSchedule $schedule)
    {
        $schedule->load('gymClass');
        return view('admin.schedules.show', compact('schedule'));
    }

    public function edit(ClassSchedule $schedule)
    {
        $gymClasses = GymClass::where('active', true)->orderBy('name')->get();
        return view('admin.schedules.edit', compact('schedule', 'gymClasses'));
    }

    public function update(Request $request, ClassSchedule $schedule)
    {
        $request->validate([
            'gym_class_id' => 'required|exists:gym_classes,id',
            'day_of_week' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_recurring' => 'boolean',
            'active' => 'boolean'
        ]);

        // Verificar que no haya conflictos de horario (excluyendo el horario actual)
        $conflict = ClassSchedule::where('day_of_week', $request->day_of_week)
            ->where('active', true)
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                          ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['time_conflict' => 'Ya existe un horario que se superpone con el horario seleccionado.'])
                        ->withInput();
        }

        $schedule->update([
            'gym_class_id' => $request->gym_class_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_recurring' => $request->has('is_recurring'),
            'active' => $request->has('active')
        ]);

        return redirect()->route('admin.schedules.show', $schedule)
                       ->with('success', 'Horario actualizado exitosamente.');
    }

    public function destroy(ClassSchedule $schedule)
    {
        try {
            // Verificar si hay reservas futuras para este horario
            $futureBookings = $schedule->gymClass->bookings()
                ->where('booking_date', '>=', now()->toDateString())
                ->where('status', 'confirmed')
                ->count();

            if ($futureBookings > 0) {
                return back()->with('error', 'No se puede eliminar el horario porque tiene reservas futuras confirmadas.');
            }

            $schedule->delete();
            
            return redirect()->route('admin.classes.show', $schedule->gym_class_id)
                           ->with('success', 'Horario eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el horario: ' . $e->getMessage());
        }
    }
}
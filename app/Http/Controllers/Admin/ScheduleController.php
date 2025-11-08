<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $schedules = Schedule::with('employees')
                ->orderBy('time_in')
                ->paginate(20);

            return view('admin.schedules.index', compact('schedules'));

        } catch (\Exception $e) {
            Log::error('Error loading schedules: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al cargar los horarios: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'time_in' => 'required|date_format:H:i',
                'time_out' => 'required|date_format:H:i|after:time_in',
            ], [
                'time_in.required' => 'La hora de entrada es obligatoria.',
                'time_in.date_format' => 'La hora de entrada debe tener el formato HH:MM.',
                'time_out.required' => 'La hora de salida es obligatoria.',
                'time_out.date_format' => 'La hora de salida debe tener el formato HH:MM.',
                'time_out.after' => 'La hora de salida debe ser posterior a la hora de entrada.',
            ]);

            // Verificar que no existe un horario igual
            $existingSchedule = Schedule::where('time_in', $validated['time_in'])
                ->where('time_out', $validated['time_out'])
                ->first();

            if ($existingSchedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un horario con el mismo horario de entrada y salida.'
                ], 422);
            }

            $schedule = Schedule::create($validated);

            Log::info('Schedule created successfully', [
                'schedule_id' => $schedule->id,
                'time_in' => $schedule->time_in,
                'time_out' => $schedule->time_out
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Horario creado exitosamente.',
                    'schedule' => $schedule
                ]);
            }

            return redirect()->route('admin.schedules.index')
                ->with('success', 'Horario creado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                    'message' => 'Datos inválidos.'
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Error creating schedule: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno del servidor.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al crear el horario: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        try {
            $validated = $request->validate([
                'time_in' => 'required|date_format:H:i',
                'time_out' => 'required|date_format:H:i|after:time_in',
            ], [
                'time_in.required' => 'La hora de entrada es obligatoria.',
                'time_in.date_format' => 'La hora de entrada debe tener el formato HH:MM.',
                'time_out.required' => 'La hora de salida es obligatoria.',
                'time_out.date_format' => 'La hora de salida debe tener el formato HH:MM.',
                'time_out.after' => 'La hora de salida debe ser posterior a la hora de entrada.',
            ]);

            // Verificar que no existe otro horario igual (excluyendo el actual)
            $existingSchedule = Schedule::where('time_in', $validated['time_in'])
                ->where('time_out', $validated['time_out'])
                ->where('id', '!=', $schedule->id)
                ->first();

            if ($existingSchedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe otro horario con el mismo horario de entrada y salida.'
                ], 422);
            }

            $schedule->update($validated);

            Log::info('Schedule updated successfully', [
                'schedule_id' => $schedule->id,
                'time_in' => $schedule->time_in,
                'time_out' => $schedule->time_out
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Horario actualizado exitosamente.',
                    'schedule' => $schedule
                ]);
            }

            return redirect()->route('admin.schedules.index')
                ->with('success', 'Horario actualizado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                    'message' => 'Datos inválidos.'
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Error updating schedule: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno del servidor.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar el horario: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Schedule $schedule)
    {
        try {
            // Verificar si el horario tiene empleados asignados
            if ($schedule->employees()->count() > 0) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede eliminar el horario porque tiene empleados asignados.'
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', 'No se puede eliminar el horario porque tiene empleados asignados.');
            }

            $schedule->delete();

            Log::info('Schedule deleted successfully', [
                'schedule_id' => $schedule->id
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Horario eliminado exitosamente.'
                ]);
            }

            return redirect()->route('admin.schedules.index')
                ->with('success', 'Horario eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error deleting schedule: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno del servidor.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar el horario: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.schedules.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        try {
            $schedule->load('employees');

            return view('admin.schedules.show', compact('schedule'));

        } catch (\Exception $e) {
            Log::error('Error loading schedule: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al cargar el horario: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        return view('admin.schedules.edit', compact('schedule'));
    }

    /**
     * Get schedules for AJAX requests.
     */
    public function getSchedules(Request $request)
    {
        try {
            $schedules = Schedule::orderBy('time_in')->get();

            return response()->json([
                'success' => true,
                'schedules' => $schedules
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting schedules: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los horarios.'
            ], 500);
        }
    }

    /**
     * Calculate schedule duration.
     */
    public function getScheduleDuration(Schedule $schedule)
    {
        try {
            $timeIn = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_in);
            $timeOut = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_out);
            
            $duration = $timeOut->diff($timeIn);
            $hours = $duration->h;
            $minutes = $duration->i;

            return response()->json([
                'success' => true,
                'duration' => [
                    'hours' => $hours,
                    'minutes' => $minutes,
                    'formatted' => "{$hours}h {$minutes}m"
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error calculating schedule duration: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al calcular la duración del horario.'
            ], 500);
        }
    }
}
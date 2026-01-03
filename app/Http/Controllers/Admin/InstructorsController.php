<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Schedule;
use App\Models\Admin;
use App\Notifications\NewEmployeeRegistered;
use Illuminate\Http\Request;

class InstructorsController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['position', 'schedule']);
        
        // Para el módulo de instructores, podemos mostrar todos los empleados
        // o filtrar por posiciones específicas según necesidades del negocio
        // Comentado el filtro por ahora para mostrar todos los empleados
        /*
        $query->whereHas('position', function($q) {
            $q->where('description', 'LIKE', '%instructor%')
              ->orWhere('description', 'LIKE', '%entrenador%')
              ->orWhere('description', 'LIKE', '%trainer%');
        });
        */

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'LIKE', "%{$search}%")
                  ->orWhere('lastname', 'LIKE', "%{$search}%")
                  ->orWhere('employee_id', 'LIKE', "%{$search}%");
            });
        }

        $instructors = $query->paginate(10);
        $positions = Position::all(); // Para los dropdowns
        $schedules = Schedule::orderBy('time_in')->get(); // Para el dropdown de horarios

        // Si es una petición AJAX, devolver solo la tabla
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.instructors.partials.table', compact('instructors'))->render(),
                'pagination' => $instructors->links()->render()
            ]);
        }

        return view('admin.instructors.index', compact('instructors', 'positions', 'schedules'));
    }

    public function show($id)
    {
        $instructor = Employee::with(['position', 'schedule'])->findOrFail($id);
        $positions = Position::all(); // Para el dropdown del modal de edición
        $schedules = Schedule::orderBy('time_in')->get(); // Para el dropdown de horarios
        
        return response()->json([
            'instructor' => $instructor,
            'positions' => $positions,
            'schedules' => $schedules
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'required|string',
            'birthdate' => 'nullable|date',
            'contact_info' => 'required|string',
            'gender' => 'required|string|in:M,F',
            'position_id' => 'required|exists:position,id',
            'schedule_id' => 'required|exists:schedules,id',
            'photo' => 'nullable|string|max:200'
        ]);

        // Generar employee_id automáticamente
        $lastEmployee = Employee::orderBy('employee_id', 'desc')->first();
        $nextId = $lastEmployee ? intval(substr($lastEmployee->employee_id, 3)) + 1 : 1;
        $employeeId = 'EMP' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $data = $request->all();
        $data['employee_id'] = $employeeId;
        $data['created_on'] = now()->format('Y-m-d');
        $data['photo'] = $data['photo'] ?? 'default.jpg';

        $instructor = Employee::create($data);

        // Notificar a todos los admins sobre el nuevo empleado
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new NewEmployeeRegistered($instructor->load('position')));
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Instructor creado exitosamente',
                'instructor' => $instructor->load(['position', 'schedule'])
            ]);
        }

        return redirect()->route('admin.instructors.index')
                         ->with('success', 'Instructor creado exitosamente');
    }

    public function update(Request $request, $id)
    {
        $instructor = Employee::findOrFail($id);

        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'required|string',
            'birthdate' => 'nullable|date',
            'contact_info' => 'required|string',
            'gender' => 'required|string|in:M,F',
            'position_id' => 'required|exists:position,id',
            'schedule_id' => 'required|exists:schedules,id',
            'photo' => 'nullable|string|max:200'
        ]);

        $instructor->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Instructor actualizado exitosamente',
                'instructor' => $instructor->load(['position', 'schedule'])
            ]);
        }

        return redirect()->route('admin.instructors.index')
                         ->with('success', 'Instructor actualizado exitosamente');
    }

    public function destroy($id)
    {
        $instructor = Employee::findOrFail($id);
        $instructor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Instructor eliminado exitosamente'
        ]);
    }
}
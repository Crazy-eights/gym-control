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

        return view('admin.classes.index', compact('classes'));
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
            'active' => 'boolean'
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

            DB::commit();
            return redirect()->route('admin.classes.index')
                           ->with('success', 'Clase creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
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
            'active' => 'boolean'
        ]);

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

        return redirect()->route('admin.classes.index')
                       ->with('success', 'Clase actualizada exitosamente.');
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
}

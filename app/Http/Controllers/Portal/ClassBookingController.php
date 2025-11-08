<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\ClassSchedule;
use App\Models\ClassBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassBookingController extends Controller
{
    public function myBookings(Request $request)
    {
        $member = Auth::user();
        $status = $request->get('status', 'upcoming');
        
        $query = ClassBooking::where('member_id', $member->id)
            ->with(['classSchedule.gymClass']);

        // Filtrar por estado
        switch ($status) {
            case 'upcoming':
                $query->whereHas('classSchedule', function($q) {
                    $q->where('start_date', '>=', Carbon::today());
                })->where('status', 'confirmed');
                break;
            case 'completed':
                $query->whereHas('classSchedule', function($q) {
                    $q->where('start_date', '<', Carbon::today());
                })->where('status', 'confirmed');
                break;
            case 'cancelled':
                $query->where('status', 'cancelled');
                break;
            case 'all':
                // No filtrar
                break;
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(12);

        // Contadores para las pestañas
        $upcomingCount = ClassBooking::where('member_id', $member->id)
            ->whereHas('classSchedule', function($q) {
                $q->where('start_date', '>=', Carbon::today());
            })
            ->where('status', 'confirmed')
            ->count();

        $completedCount = ClassBooking::where('member_id', $member->id)
            ->whereHas('classSchedule', function($q) {
                $q->where('start_date', '<', Carbon::today());
            })
            ->where('status', 'confirmed')
            ->count();

        $cancelledCount = ClassBooking::where('member_id', $member->id)
            ->where('status', 'cancelled')
            ->count();

        $totalCount = ClassBooking::where('member_id', $member->id)->count();

        return view('portal.bookings.index', compact(
            'bookings', 
            'upcomingCount', 
            'completedCount', 
            'cancelledCount', 
            'totalCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:class_schedules,id'
        ]);

        $member = Auth::user();
        $schedule = ClassSchedule::with('gymClass')->findOrFail($request->schedule_id);

        // Verificar si la clase está activa
        if (!$schedule->gymClass->active) {
            return back()->with('error', 'Esta clase no está disponible actualmente.');
        }

        // Verificar si ya tiene una reserva para esta sesión
        $existingBooking = ClassBooking::where('member_id', $member->id)
            ->where('class_schedule_id', $schedule->id)
            ->where('status', 'confirmed')
            ->first();

        if ($existingBooking) {
            return back()->with('error', 'Ya tienes una reserva para esta sesión.');
        }

        // Verificar capacidad disponible
        $currentBookings = ClassBooking::where('class_schedule_id', $schedule->id)
            ->where('status', 'confirmed')
            ->count();

        if ($currentBookings >= $schedule->gymClass->max_participants) {
            return back()->with('error', 'Esta sesión está llena. No hay cupos disponibles.');
        }

        // Verificar que la sesión no haya comenzado
        $sessionDateTime = Carbon::parse($schedule->start_date . ' ' . $schedule->start_time);
        if ($sessionDateTime <= now()) {
            return back()->with('error', 'No se puede reservar una sesión que ya comenzó.');
        }

        // Crear la reserva
        ClassBooking::create([
            'member_id' => $member->id,
            'class_schedule_id' => $schedule->id,
            'booking_date' => now()->toDateString(),
            'status' => 'confirmed',
            'booked_at' => now()
        ]);

        return back()->with('success', 'Reserva confirmada exitosamente para ' . $schedule->gymClass->name . ' el ' . Carbon::parse($schedule->start_date)->format('d/m/Y') . ' a las ' . Carbon::parse($schedule->start_time)->format('H:i') . '.');
    }

    public function cancel(ClassBooking $booking)
    {
        $member = Auth::user();

        // Verificar que la reserva pertenece al usuario
        if ($booking->member_id !== $member->id) {
            return back()->with('error', 'No tienes permiso para cancelar esta reserva.');
        }

        // Verificar que la reserva esté confirmada
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Esta reserva no se puede cancelar.');
        }

        // Verificar que falten al menos 2 horas para la clase
        $schedule = $booking->classSchedule;
        $sessionDateTime = Carbon::parse($schedule->start_date . ' ' . $schedule->start_time);
        $now = now();

        if ($sessionDateTime->subHours(2) <= $now) {
            return back()->with('error', 'No se puede cancelar una reserva con menos de 2 horas de anticipación.');
        }

        // Cancelar la reserva
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return back()->with('success', 'Reserva cancelada exitosamente.');
    }

    // Método para obtener disponibilidad (API)
    public function getAvailability($classId, Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $schedules = ClassSchedule::where('gym_class_id', $classId)
            ->where('start_date', '<=', $date)
            ->where(function($query) use ($date) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $date);
            })
            ->with(['bookings' => function($q) use ($date) {
                $q->where('status', 'confirmed')
                  ->where('booking_date', $date);
            }])
            ->get();

        $availability = $schedules->map(function($schedule) {
            $confirmedBookings = $schedule->bookings->count();
            $capacity = $schedule->gymClass->max_participants;
            
            return [
                'schedule_id' => $schedule->id,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'available_spots' => $capacity - $confirmedBookings,
                'total_capacity' => $capacity,
                'is_full' => $confirmedBookings >= $capacity
            ];
        });

        return response()->json($availability);
    }

    // Métodos faltantes para completar la funcionalidad

    public function availableClasses()
    {
        // Por ahora devolver una vista básica o redireccionar
        return view('portal.clases', [
            'clases' => collect([]) // Empty collection por ahora
        ]);
    }

    public function classDetails($gymClass)
    {
        // Método temporal 
        return back()->with('info', 'Funcionalidad en desarrollo');
    }

    public function book(Request $request)
    {
        // Método temporal - usar store en su lugar
        return $this->store($request);
    }

    public function show($booking)
    {
        // Método temporal
        return back()->with('info', 'Funcionalidad en desarrollo');
    }
}
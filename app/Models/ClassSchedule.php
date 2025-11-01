<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_class_id',
        'day_of_week',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'is_recurring',
        'active'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'active' => 'boolean'
    ];

    // Relación con la clase
    public function gymClass()
    {
        return $this->belongsTo(GymClass::class);
    }

    // Relación con reservas
    public function bookings()
    {
        return $this->hasMany(ClassBooking::class);
    }

    // Obtener reservas para una fecha específica
    public function bookingsForDate($date)
    {
        return $this->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled');
    }

    // Verificar disponibilidad para una fecha
    public function isAvailableForDate($date)
    {
        if (!$this->active) return false;
        
        $confirmedBookings = $this->bookingsForDate($date)->count();
        return $confirmedBookings < $this->gymClass->max_participants;
    }

    // Obtener espacios disponibles para una fecha
    public function availableSpotsForDate($date)
    {
        $confirmedBookings = $this->bookingsForDate($date)->count();
        return max(0, $this->gymClass->max_participants - $confirmedBookings);
    }

    // Verificar si el horario es válido para una fecha específica
    public function isValidForDate($date)
    {
        $dayName = Carbon::parse($date)->locale('es')->dayName;
        $dayMap = [
            'lunes' => 'Monday',
            'martes' => 'Tuesday', 
            'miércoles' => 'Wednesday',
            'jueves' => 'Thursday',
            'viernes' => 'Friday',
            'sábado' => 'Saturday',
            'domingo' => 'Sunday'
        ];
        
        $englishDay = array_search($dayName, $dayMap);
        if ($englishDay !== false && $englishDay !== $this->day_of_week) {
            return false;
        }

        // Verificar rango de fechas si está definido
        if ($this->start_date && Carbon::parse($date)->lt($this->start_date)) {
            return false;
        }
        
        if ($this->end_date && Carbon::parse($date)->gt($this->end_date)) {
            return false;
        }

        return true;
    }
}

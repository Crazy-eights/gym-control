<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'class_schedule_id',
        'booking_date',
        'status',
        'booked_at',
        'cancelled_at',
        'notes'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booked_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    // Relación con miembro
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Relación con horario de clase
    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    // Relación con clase a través del horario
    public function gymClass()
    {
        return $this->hasOneThrough(GymClass::class, ClassSchedule::class, 'id', 'id', 'class_schedule_id', 'gym_class_id');
    }

    // Scopes para filtros comunes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('booking_date', $date);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
                    ->whereIn('status', ['confirmed']);
    }

    public function scopeAttended($query)
    {
        return $query->where('status', 'attended');
    }

    // Cancelar reserva
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);
    }

    // Marcar como asistido
    public function markAsAttended()
    {
        $this->update(['status' => 'attended']);
    }

    // Marcar como no asistido
    public function markAsNoShow()
    {
        $this->update(['status' => 'no_show']);
    }
}

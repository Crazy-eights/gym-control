<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'instructor_name',
        'duration_minutes',
        'max_participants',
        'price',
        'difficulty_level',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'price' => 'decimal:2'
    ];

    // Relación con horarios de clase
    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    // Relación con reservas a través de horarios
    public function bookings()
    {
        return $this->hasManyThrough(ClassBooking::class, ClassSchedule::class);
    }

    // Obtener horarios activos
    public function activeSchedules()
    {
        return $this->schedules()->where('active', true);
    }

    // Obtener próximas sesiones de esta clase
    public function upcomingSessions($limit = 10)
    {
        return $this->activeSchedules()
            ->where('start_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->limit($limit)
            ->get();
    }
}

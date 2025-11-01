<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Member extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * La tabla SÍ usa 'created_at' y 'updated_at' según la BD.
     */
    public $timestamps = true;

    /**
     * Atributos "llenables".
     */
    protected $fillable = [
        'member_id',
        'firstname',
        'lastname',
        'address',
        'birthdate',
        'contact_info',
        'email',
        'password',
        'gender',
        'plan_id',
        'subscription_start_date',
        'subscription_end_date',
        'photo',
    ];

    /**
     * Atributos que deben ser ocultados en arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * RELACIÓN: Un miembro "pertenece a" un Plan de Membresía.
     */
    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id', 'id');
    }

    /**
     * RELACIÓN: Un miembro puede tener muchas asistencias.
     */
    public function attendances()
    {
        return $this->hasMany(MemberAttendance::class);
    }

    /**
     * RELACIÓN: Un miembro puede tener muchas reservas de clases.
     */
    public function classBookings()
    {
        return $this->hasMany(ClassBooking::class);
    }

    /**
     * Obtener reservas de clases confirmadas.
     */
    public function confirmedBookings()
    {
        return $this->classBookings()->confirmed();
    }

    /**
     * Obtener próximas clases reservadas.
     */
    public function upcomingClasses()
    {
        return $this->classBookings()
            ->upcoming()
            ->with(['classSchedule.gymClass'])
            ->orderBy('booking_date')
            ->orderBy('created_at');
    }

    /**
     * Obtener historial de clases asistidas.
     */
    public function attendedClasses()
    {
        return $this->classBookings()
            ->attended()
            ->with(['classSchedule.gymClass'])
            ->orderBy('booking_date', 'desc');
    }

    /**
     * Verificar si el miembro puede reservar más clases según su plan.
     */
    public function canBookClass()
    {
        if (!$this->is_active) return false;
        
        // Aquí se pueden agregar lógicas específicas según el plan
        // Por ejemplo, límites mensuales de clases según el tipo de membresía
        return true;
    }

    /**
     * Obtiene el nombre completo del socio.
     */
    public function getFullNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Verifica si la membresía está activa.
     */
    public function getIsActiveAttribute()
    {
        if (!$this->subscription_end_date || !$this->plan_id) {
            return false;
        }

        $endDate = $this->subscription_end_date instanceof \Carbon\Carbon 
            ? $this->subscription_end_date 
            : \Carbon\Carbon::parse($this->subscription_end_date);

        return $endDate->isAfter(now());
    }

    /**
     * Obtiene el estado de la membresía.
     */
    public function getStatusAttribute()
    {
        if (!$this->subscription_end_date || !$this->plan_id) {
            return 'sin_plan';
        }

        $now = now();
        $endDate = $this->subscription_end_date instanceof \Carbon\Carbon 
            ? $this->subscription_end_date 
            : \Carbon\Carbon::parse($this->subscription_end_date);

        if ($endDate->isAfter($now)) {
            // Verificar si está próximo a vencer (7 días)
            if ($endDate->diffInDays($now) <= 7) {
                return 'proximo_vencimiento';
            }
            return 'activo';
        }

        return 'vencido';
    }

    /**
     * Scope para miembros activos.
     */
    public function scopeActive($query)
    {
        return $query->where('subscription_end_date', '>=', now());
    }

    /**
     * Scope para miembros vencidos.
     */
    public function scopeExpired($query)
    {
        return $query->where('subscription_end_date', '<', now());
    }

    /**
     * Casting de fechas.
     */
    protected $casts = [
        'birthdate' => 'date',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'email_verified_at' => 'datetime',
    ];
}
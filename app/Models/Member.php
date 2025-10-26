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
        return $this->subscription_end_date && 
               now()->lessThanOrEqualTo($this->subscription_end_date);
    }

    /**
     * Obtiene el estado de la membresía.
     */
    public function getStatusAttribute()
    {
        if (!$this->subscription_end_date) {
            return 'sin_plan';
        }

        $now = now();
        $endDate = \Carbon\Carbon::parse($this->subscription_end_date);

        if ($endDate->isFuture()) {
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
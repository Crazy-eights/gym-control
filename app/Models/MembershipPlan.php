<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    use HasFactory;

    /**
     * La tabla SÍ usa 'created_at' y 'updated_at' para auditoría.
     */
    public $timestamps = true;

    /**
     * Atributos "llenables".
     */
    protected $fillable = [
        'plan_name',
        'description',
        'price',
        'duration_days',
    ];

    /**
     * RELACIÓN: Un plan "tiene muchos" Miembros.
     */
    public function members()
    {
        return $this->hasMany(Member::class, 'plan_id', 'id');
    }
}
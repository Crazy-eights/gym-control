<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    /**
     * La tabla se llama 'schedules', Laravel la encuentra automáticamente.
     * public $timestamps = false;
     */
    
    /**
     * La tabla no usa 'created_at' y 'updated_at'.
     */
    public $timestamps = false;

    /**
     * Atributos "llenables".
     */
    protected $fillable = [
        'time_in',
        'time_out',
    ];

    /**
     * RELACIÓN: Un horario "tiene muchos" Empleados.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'schedule_id', 'id');
    }
}
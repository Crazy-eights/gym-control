<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla.
     * (Por defecto, Laravel buscaría 'attendances')
     */
    protected $table = 'attendance';

    /**
     * La tabla no usa 'created_at' y 'updated_at'.
     */
    public $timestamps = false;

    /**
     * Atributos "llenables".
     */
    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'status',
        'time_out',
        'num_hr',
    ];

    /**
     * RELACIÓN: La asistencia "pertenece a" un Empleado.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
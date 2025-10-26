<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * La tabla no usa los timestamps 'created_at' y 'updated_at'.
     * (Porque usamos 'created_on')
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'employee_id',
        'firstname',
        'lastname',
        'address',
        'birthdate',
        'contact_info',
        'gender',
        'position_id',
        'schedule_id',
        'photo',
        'created_on',
    ];

    /**
     * RELACIÓN: Un empleado "pertenece a" un Puesto (Position).
     */
    public function position()
    {
        // 'foreignKey' (opcional si sigue la convención) -> 'ownerKey' (opcional si es 'id')
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    /**
     * RELACIÓN: Un empleado "pertenece a" un Horario (Schedule).
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id');
    }

    /**
     * RELACIÓN: Un empleado "tiene muchas" Asistencias (Attendance).
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla.
     * (Por defecto, Laravel buscaría 'positions' en plural)
     */
    protected $table = 'position';

    /**
     * La tabla no usa 'created_at' y 'updated_at'.
     */
    public $timestamps = false;

    /**
     * Atributos "llenables".
     */
    protected $fillable = [
        'description',
        'rate',
    ];

    /**
     * RELACIÓN: Un puesto "tiene muchos" Empleados.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// ¡Importante! Usamos el modelo de autenticación de Laravel
use Illuminate\Foundation\Auth\User as Authenticatable; 

class Admin extends Authenticatable // <-- Extiende de Authenticatable
{
    use HasFactory;

    /**
     * El nombre de la tabla.
     */
    protected $table = 'admin';

    /**
     * La tabla no usa 'created_at' y 'updated_at'.
     * (Usa 'created_on')
     */
    public $timestamps = false;

    /**
     * Atributos "llenables".
     */
    protected $fillable = [
        'username',
        'password',
        'firstname',
        'lastname',
        'photo',
        'created_on',
        'email',
    ];

    /**
     * Atributos que deben ocultarse (como la contraseña).
     */
    protected $hidden = [
        'password',
    ];

    /**
     * --------------------------------------------------
     * ¡¡ESTA ES LA LÍNEA QUE ARREGLA EL LOGIN!!
     * Le dice a Laravel que use la columna 'username' 
     * en lugar de 'email' para autenticar.
     * --------------------------------------------------
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}
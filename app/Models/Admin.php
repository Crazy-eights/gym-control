<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable 
{
    use HasFactory;

    /**
     * El nombre de la tabla.
     */
    protected $table = 'admin';


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
     * Indica a Laravel que use 'username' para la autenticación.
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}

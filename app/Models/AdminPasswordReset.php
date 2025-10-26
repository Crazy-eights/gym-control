<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AdminPasswordReset extends Model
{
    use HasFactory;

    protected $table = 'admin_password_resets';

    // No usar created_at y updated_at automáticos
    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected $dates = [
        'created_at',
    ];

    /**
     * Verificar si el token ha expirado
     */
    public function isExpired()
    {
        // Tokens expiran después de 60 minutos
        return Carbon::parse($this->created_at)->addMinutes(60)->isPast();
    }

    /**
     * Obtener admin asociado al email
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'email', 'email');
    }

    /**
     * Crear un nuevo token de reset
     */
    public static function createToken($email)
    {
        // Eliminar tokens anteriores para este email
        self::where('email', $email)->delete();

        // Crear nuevo token
        $token = \Illuminate\Support\Str::random(64);

        return self::create([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);
    }

    /**
     * Buscar token válido
     */
    public static function findValidToken($token, $email)
    {
        $reset = self::where('token', $token)
                    ->where('email', $email)
                    ->first();

        if ($reset && !$reset->isExpired()) {
            return $reset;
        }

        return null;
    }

    /**
     * Limpiar tokens expirados
     */
    public static function cleanExpiredTokens()
    {
        self::where('created_at', '<', now()->subMinutes(60))->delete();
    }
}

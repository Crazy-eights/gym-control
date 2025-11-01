<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAttendance extends Model
{
    use HasFactory;

    // Especificar el nombre correcto de la tabla
    protected $table = 'member_attendance';

    protected $fillable = [
        'member_id',
        'checkin_time',
        'attendance_date'
    ];

    protected $casts = [
        'checkin_time' => 'datetime',
        'attendance_date' => 'date'
    ];

    // Relación con Member
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

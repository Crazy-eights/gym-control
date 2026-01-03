<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewEmployeeRegistered extends Notification
{
    use Queueable;

    protected $employee;

    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $positionName = $this->employee->position ? $this->employee->position->description : 'Sin posición';
        
        return [
            'type' => 'new_employee',
            'title' => 'Nuevo empleado registrado',
            'message' => "Se ha registrado un nuevo empleado: {$this->employee->firstname} {$this->employee->lastname} - {$positionName}",
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->firstname . ' ' . $this->employee->lastname,
            'position' => $positionName,
            'icon' => 'fa-user-plus',
            'color' => 'success',
            'url' => route('admin.instructors.show', $this->employee->id)
        ];
    }
}

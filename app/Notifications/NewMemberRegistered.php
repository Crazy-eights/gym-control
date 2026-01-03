<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMemberRegistered extends Notification
{
    use Queueable;

    protected $member;

    public function __construct($member)
    {
        $this->member = $member;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_member',
            'title' => 'Nuevo socio registrado',
            'message' => "Se ha registrado un nuevo socio: {$this->member->firstname} {$this->member->lastname}",
            'member_id' => $this->member->id,
            'member_name' => $this->member->firstname . ' ' . $this->member->lastname,
            'icon' => 'fa-user-plus',
            'color' => 'success',
            'url' => route('admin.socios.show', $this->member->id)
        ];
    }
}

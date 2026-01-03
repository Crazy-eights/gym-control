<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MemberSuspended extends Notification
{
    use Queueable;

    protected $member;
    protected $reason;

    public function __construct($member, $reason = null)
    {
        $this->member = $member;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $message = "El socio {$this->member->firstname} {$this->member->lastname} ha sido suspendido";
        if ($this->reason) {
            $message .= ": {$this->reason}";
        }

        return [
            'type' => 'member_suspended',
            'title' => 'Socio suspendido',
            'message' => $message,
            'member_id' => $this->member->id,
            'member_name' => $this->member->firstname . ' ' . $this->member->lastname,
            'reason' => $this->reason,
            'icon' => 'fa-user-slash',
            'color' => 'danger',
            'url' => route('admin.socios.show', $this->member->id)
        ];
    }
}

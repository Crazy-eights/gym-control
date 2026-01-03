<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class MembershipExpiring extends Notification
{
    use Queueable;

    protected $member;
    protected $daysLeft;

    public function __construct($member, $daysLeft)
    {
        $this->member = $member;
        $this->daysLeft = $daysLeft;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $message = $this->daysLeft < 0 
            ? "La membresía de {$this->member->firstname} {$this->member->lastname} venció hace " . abs($this->daysLeft) . " días"
            : ($this->daysLeft == 0 
                ? "La membresía de {$this->member->firstname} {$this->member->lastname} vence HOY"
                : "La membresía de {$this->member->firstname} {$this->member->lastname} vence en {$this->daysLeft} días");
        
        $color = $this->daysLeft < 0 ? 'danger' : ($this->daysLeft <= 3 ? 'warning' : 'info');
        
        return [
            'type' => 'membership_expiring',
            'title' => $this->daysLeft < 0 ? 'Membresía vencida' : 'Membresía por vencer',
            'message' => $message,
            'member_id' => $this->member->id,
            'member_name' => $this->member->firstname . ' ' . $this->member->lastname,
            'days_left' => $this->daysLeft,
            'icon' => 'fa-calendar-times',
            'color' => $color,
            'url' => route('admin.socios.show', $this->member->id)
        ];
    }
}

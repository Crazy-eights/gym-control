<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification
{
    use Queueable;

    protected $member;
    protected $amount;

    public function __construct($member, $amount)
    {
        $this->member = $member;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_received',
            'title' => 'Pago recibido',
            'message' => "Se ha recibido un pago de \${$this->amount} de {$this->member->firstname} {$this->member->lastname}",
            'member_id' => $this->member->id,
            'member_name' => $this->member->firstname . ' ' . $this->member->lastname,
            'amount' => $this->amount,
            'icon' => 'fa-dollar-sign',
            'color' => 'success',
            'url' => route('admin.socios.show', $this->member->id)
        ];
    }
}

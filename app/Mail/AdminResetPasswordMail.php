<?php

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $resetUrl;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Admin $admin, $resetUrl, $token)
    {
        $this->admin = $admin;
        $this->resetUrl = $resetUrl;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.admin.reset-password')
                    ->subject('Restablecer Contraseña - Gym Control Admin')
                    ->with([
                        'admin' => $this->admin,
                        'resetUrl' => $this->resetUrl,
                        'token' => $this->token,
                    ]);
    }
}

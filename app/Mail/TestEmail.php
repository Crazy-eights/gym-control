<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    public $testData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $testData = [])
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->testData = array_merge([
            'timestamp' => now()->format('d/m/Y H:i:s'),
            'system_name' => 'Gym Control System',
            'test_id' => uniqid('test_'),
        ], $testData);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.test')
                    ->with([
                        'content' => $this->content,
                        'testData' => $this->testData,
                    ]);
    }
}

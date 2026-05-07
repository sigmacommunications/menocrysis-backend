<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $supportData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($supportData)
    {
        $this->supportData = $supportData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Support Request: ' . $this->supportData['subject'])
                    ->view('emails.support');
    }
}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $user = null;
    public $otp = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($props)
    {
        $this->user = $props['user'];
        $this->otp = $props['otp'];
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Otp Mailer',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function build()
    {
        return $this->subject('One Time Password - Dailyhotels.id')->view('emails.otp', [
            'user' => $this->user,
            'otp' => $this->otp,
        ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}

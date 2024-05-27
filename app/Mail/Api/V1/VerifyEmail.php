<?php

namespace App\Mail\Api\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $name, $token, $prefix, $url;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $token, $prefix = 'users')
    {
        $this->name   = $name;
        $this->token  = $token;
        $this->prefix = $prefix;
        $this->url    = config('app.frontend_url') . $this->prefix .'/email-verify/confirm/' . $this->token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@jonstuart', 'Admin - Jon Stuart'),
            subject: 'Verify Email',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.VerifyEmail',[
            'name' => $this->name,
            'url'  => $this->url,
        ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

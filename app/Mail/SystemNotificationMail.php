<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $messageText;
    public $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $messageText, $actionUrl = '')
    {
        $this->user = $user;
        $this->messageText = $messageText;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $siteName = \App\Models\Setting::first()->titer ?? config('app.name', 'MYADS');
        
        return new Envelope(
            subject: '[' . $siteName . '] New Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
        );
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

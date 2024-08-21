<?php

namespace App\Mail\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobseekerUnrestrictedChatEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $room;
    protected $user;
    protected $link;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $room, $link)
    {
        $this->user = $user;
        $this->room = $room;
        $this->link = $link;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: trans(
                'lang.chat.unrestricted.envelope_title',
                [],
                getUserLanguage($this->user)
            )
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $template =
            getUserLanguage($this->user) == 'en'
                ? 'emails.jobseeker-unrestricted-chat-en'
                : 'emails.jobseeker-unrestricted-chat-ja';
        return new Content(
            view: $template,
            with: [
                'room' => $this->room,
                'user' => $this->user,
                'payment_link' => $this->link,
            ]
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

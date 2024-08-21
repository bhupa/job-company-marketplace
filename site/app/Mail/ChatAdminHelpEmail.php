<?php

namespace App\Mail;

use App\Models\ChatRoom;
use App\Models\Company;
use App\Models\Jobseeker;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChatAdminHelpEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $chatroom;
    protected $company;
    protected $jobseeker;

    /**
     * Create a new message instance.
     */
    public function __construct(
        ChatRoom $chatroom,
        Company $company,
        Jobseeker $jobseeker
    ) {
        $this->chatroom = $chatroom;
        $this->company = $company;
        $this->jobseeker = $jobseeker;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Need assits for chat');
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.chat-admin-assits',
            with: [
                'chatroom' => $this->chatroom,
                'company' => $this->company,
                'jobseeker' => $this->jobseeker,
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

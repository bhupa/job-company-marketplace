<?php

namespace App\Mail\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class SubscriptionEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $subscription;
    protected $plan;
    protected $user;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $plan)
    {
        // $this->subscription = $subscription;
        $this->plan = $plan;
        $this->user = $user;

        Log::info('subscription plane mail for template');
        Log::info($this->plan);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: trans('lang.subscribe', [], getUserLanguage($this->user)) .
                ' ' .
                $this->plan['contractPeriod'] .
                ' ' .
                trans('lang.plan', [], getUserLanguage($this->user))
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('subscription plane mail for content');
        return new Content(
            view: 'emails.subscription-email-notification',
            with: [
                'plan' => $this->plan,
                'user' => $this->user,
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

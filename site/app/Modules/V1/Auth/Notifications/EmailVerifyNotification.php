<?php

namespace App\Modules\V1\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerifyNotification extends Notification
{
    use Queueable;
    public $url;
    protected $user;
    protected $output;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $output)
    {
        $this->user = $user;
        $this->output = $output;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Verify Your Email')
            ->view('user::emails.email-verification-notification', [
                'user' => $this->user,
                'link' => $this->output,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
        ];
    }
}

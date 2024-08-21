<?php

namespace App\Notifications\V1\Chat;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UnrestrictedChatNotification extends Notification
{
    use Queueable;

    protected $output;
    protected $notificationId;
    protected $createdBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($notificationId, $createdBy, $output)
    {
        $this->notificationId = $notificationId;
        $this->createdBy = $createdBy;
        $this->output = $output;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        return [
            'id' => $this->notificationId,
            'createdBy' => $this->createdBy,
            'data' => $this->output,
        ];
    }
}

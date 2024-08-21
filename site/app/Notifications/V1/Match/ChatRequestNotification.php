<?php

namespace App\Notifications\V1\Match;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ChatRequestNotification extends Notification
{
    use Queueable;

    protected $output;
    protected $model;
    protected $notificationId;
    protected $createdBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($notificationId, $createdBy, $output)
    {
        $this->output = $output;
        $this->notificationId = $notificationId;
        $this->createdBy = $createdBy;
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

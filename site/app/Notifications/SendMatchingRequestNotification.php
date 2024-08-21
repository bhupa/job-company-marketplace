<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SendMatchingRequestNotification extends Notification
{
    use Queueable;

    protected $output;
    protected $type;
    protected $user;
    protected $model;
    protected $notificationId;

    /**
     * Create a new notification instance.
     */
    public function __construct($notificationId, $type, $user, $model, $output)
    {
        $this->output = $output;
        $this->type = $type;
        $this->user = $user;
        $this->model = $model;
        $this->notificationId = $notificationId;
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
            'type' => $this->type,
            'notifiable_id' => $this->user->id,
            'notifiable_type' => $this->model,
            'data' => $this->output,
        ];
    }
}

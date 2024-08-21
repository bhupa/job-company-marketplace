<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommonNotification extends Notification
{
    use Queueable;

    protected $output;
    protected $type;
    protected $user;
    protected $model;
    protected $notificationId;
    protected $createdBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        $notificationId,
        $type,
        $user,
        $model,
        $createdBy,
        $output
    ) {
        $this->notificationId = $notificationId;
        $this->type = $type;
        $this->user = $user;
        $this->model = $model;
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
            'type' => $this->type,
            'notifiable_id' => $this->user,
            'notifiable_type' => $this->model,
            'data' => $this->output,
            'created_by' => $this->createdBy,
        ];
    }

    /*
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
}

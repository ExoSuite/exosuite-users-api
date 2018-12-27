<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewGroupNotification extends ExoSuiteNotification
{
    private $message;
    private $group;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $message, array $group)
    {
        $this->message = $message;
        $this->group = $group;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'data' => [
                "message" => $this->message,
                "group" => $this->group
            ],
            'notification_type' => NotificationType::NEW_GROUP
        ];
    }
}

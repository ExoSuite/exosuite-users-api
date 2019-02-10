<?php

namespace App\Notifications;

use App\Enums\NotificationType;

/**
 * Class DeletedGroupNotification
 * @package App\Notifications
 */
class DeletedGroupNotification extends ExoSuiteNotification
{
    /**
     * @var array|string
     */
    private $message;
    /**
     * @var array
     */
    private $group;

    /**
     * Create a new notification instance.
     *
     * @param string|array $message
     * @param array $group
     */
    public function __construct($message, array $group)
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
            'notification_type' => NotificationType::DELETED_GROUP
        ];
    }
}

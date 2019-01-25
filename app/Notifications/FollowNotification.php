<?php

namespace App\Notifications;

use App\Enums\NotificationType;

/**
 * Class FollowNotification
 * @package App\Notifications
 */
class FollowNotification extends ExoSuiteNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
            'data' => 'new follow!',
            'notification_type' => NotificationType::FOLLOW
        ];
    }
}

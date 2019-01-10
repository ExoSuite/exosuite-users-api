<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class NotificationType
 * @package App\Enums
 */
final class NotificationType extends Enum
{
    /**
     * follow notification
     */
    const FOLLOW = "follow";
    /**
     * new message notification
     */
    const NEW_MESSAGE = "new_message";
}

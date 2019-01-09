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
     *
     */
    const FOLLOW = "follow";
    /**
     *
     */
    const NEW_MESSAGE = "new_message";
    /**
     *
     */
    const NEW_GROUP = "new_group";
}

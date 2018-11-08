<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationType extends Enum
{
    const FOLLOW = "follow";
    const NEW_MESSAGE = "new_message";
}

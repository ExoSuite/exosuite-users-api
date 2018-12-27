<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class BindType extends Enum
{
    const GROUP = "group_id";
    const MESSAGE = "message_id";
    const UUID = "uuid";
}

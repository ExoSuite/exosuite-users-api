<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class BindType extends Enum
{
    const GROUP = "group";
    const MESSAGE = "message";
    const NOTIFICATION = "notification";
    const UUID = "uuid";
}

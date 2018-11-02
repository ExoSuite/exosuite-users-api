<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Queue extends Enum
{
    const NOTIFICATION = "notifications";
    const MAIL = 'mail';
    const MESSAGE = 'messages';
    const DEFAULT = 'default';
}

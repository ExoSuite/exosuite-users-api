<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class Queue
 *
 * @package App\Enums
 */
final class Queue extends Enum
{

    const NOTIFICATION = "notifications";
    const MAIL = 'mail';
    const MESSAGE = 'messages';
    const DEFAULT = 'default';
}

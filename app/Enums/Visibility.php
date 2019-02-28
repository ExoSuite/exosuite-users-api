<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class Visibility define visibility of a resource
 *
 * @package App\Enums
 */
final class Visibility extends Enum
{

    /**
     * public run
     */
    const PUBLIC = 'public';
    /**
     * private run
     */
    const PRIVATE = 'private';
}

<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class RunVisibility define visibility of a \App\Models\Run
 * @package App\Enums
 */
final class RunVisibility extends Enum
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

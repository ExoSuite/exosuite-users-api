<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class CheckPointType define type of an \App\Models\CheckPoint
 * @package App\Enums
 */
final class CheckPointType extends Enum
{
    /**
     * start of the Run
     */
    const START = 'start';
    /**
     * arrival of the Run
     */
    const ARRIVAL = 'arrival';
    /**
     * default value for checkpoints
     */
    const DEFAULT = 'checkpoint';
}

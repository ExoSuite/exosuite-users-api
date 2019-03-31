<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class CheckPointType define type of a \App\Models\CheckPoint
 *
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

    /**
     * @return bool
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public function isArrivalOrDefault(): bool
    {
        return $this->is(CheckPointType::ARRIVAL) || $this->is(CheckPointType::DEFAULT);
    }
}

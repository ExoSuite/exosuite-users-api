<?php

namespace App\Rules;

use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\Run;
use Illuminate\Contracts\Validation\Rule;

class CheckPointUpdateTypeRule implements Rule
{
    /**
     * @var Run
     */
    private $run;

    /**
     * @var CheckPoint
     */
    private $checkPoint;

    /**
     * Create a new rule instance.
     *
     * @param Run $run
     * @param CheckPoint $checkPoint
     */
    public function __construct(Run $run, CheckPoint $checkPoint)
    {
        $this->run = $run;
        $this->checkPoint = $checkPoint;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $checkpoints = $this->run->checkpoints()->get()->toArray();
        if (!CheckPointType::hasValue($value)) {
            return false;
        } else {
            if ($value === CheckPointType::START) {
                return $this->checkPointTypeIntegrity($checkpoints, CheckPointType::START);
            }
            else if ($value === CheckPointType::ARRIVAL) {
                // if the value if found checkPointTypeIntegrity will return false so we need to invert the result
                $isStartHere = !$this->checkPointTypeIntegrity($checkpoints, CheckPointType::START);
                if ($isStartHere === false)
                    return false;
                return $this->checkPointTypeIntegrity($checkpoints, CheckPointType::ARRIVAL);
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('checkpoint.bad_type');
    }

    private function checkPointTypeIntegrity(array $checkpoints, string $checkPointType): bool
    {
        foreach ($checkpoints as $checkpt) {
            if ($checkpt['id'] === $this->checkPoint->id)
                continue;

            if ($checkpt['type'] === $checkPointType) {
                return false;
            }
        }

        return true;
    }
}

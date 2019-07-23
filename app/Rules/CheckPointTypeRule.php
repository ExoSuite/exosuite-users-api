<?php

namespace App\Rules;

use App\Enums\CheckPointType;
use App\Models\Run;
use Illuminate\Contracts\Validation\Rule;

class CheckPointTypeRule implements Rule
{
    /**
     * @var Run
     */
    private $run;

    /**
     * Create a new rule instance.
     *
     * @param Run $run
     */
    public function __construct(Run $run)
    {
        $this->run = $run;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
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
                // if a start checkpoint is found, checkPointTypeIntegrity will return false
                $isStartHere = !$this->checkPointTypeIntegrity($checkpoints, CheckPointType::START);
                if ($isStartHere === false)
                    return false;
                return $this->checkPointTypeIntegrity($checkpoints, CheckPointType::ARRIVAL);
            }
            else {
                // if a start checkpoint is found, checkPointTypeIntegrity will return false
                $isStartHere = !$this->checkPointTypeIntegrity($checkpoints, CheckPointType::START);
                if ($isStartHere === false)
                    return false;
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
            if ($checkpt['type'] === $checkPointType) {
                return false;
            }
        }
        return true;
    }
}

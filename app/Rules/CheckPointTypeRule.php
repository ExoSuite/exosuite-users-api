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
        $isStartHere = false;
        if (!CheckPointType::hasValue($value)) {
            return false;
        } else {
            if ($value === CheckPointType::START) {
                $checkpoints = $this->run->checkpoints()->get()->toArray();
                foreach ($checkpoints as $checkpt) {
                    if ($checkpt['type'] === CheckPointType::START) {
                        return false;
                    }
                }
            }
            else if ($value === CheckPointType::ARRIVAL) {
                $checkpoints = $this->run->checkpoints()->get()->toArray();
                foreach ($checkpoints as $checkpt) {
                    if ($checkpt['type'] === CheckPointType::START) {
                        $isStartHere = true;
                        break;
                    }
                }
                if ($isStartHere === false)
                    return false;
                foreach ($checkpoints as $checkpt) {
                    if ($checkpt['type'] === CheckPointType::ARRIVAL) {
                        return false;
                    }
                }
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
        return 'Bad CheckPoint type for CheckPoint.';
    }
}

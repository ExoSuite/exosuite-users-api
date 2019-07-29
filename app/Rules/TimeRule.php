<?php

namespace App\Rules;

use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\Run;
use App\Models\Time;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class TimeRule
 * @package App\Rules
 */
class TimeRule implements Rule
{
    /**
     * @var \App\Models\Run
     */
    private $run;

    /**
     * @var \App\Models\CheckPoint
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
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public function passes($attribute, $value)
    {
        /** @var \App\Enums\CheckPointType $checkPointType */
        $checkPointType = CheckPointType::getInstance($this->checkPoint->type);
        $min = Carbon::create(2015, 12, 31, 23, 59, 59);
        $max = Carbon::create(2025, 12, 31, 23, 59, 59);
        $date = Carbon::createFromTimeStamp((int)$value);

        if ($checkPointType->isArrivalOrDefault()) {
            $last_checkpoint = CheckPoint::whereId($this->checkPoint->previous_checkpoint_id)->firstOrFail();
            $last_checkpoint_time = $last_checkpoint->times()->latest()->first();
            $last_checkpoint_time_timestamp = Time::findOrFail($last_checkpoint_time->id)->first();
            return (
                $date->gt($min) &&
                $date->lte($max) &&
                $value > $last_checkpoint_time_timestamp->current_time
            );
        } else {
            return $date->gt($min) && $date->lte($max);
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}

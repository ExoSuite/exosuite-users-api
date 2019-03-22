<?php

namespace App\Rules;

use App\Models\Run;
use App\Models\CheckPoint;
use App\Enums\CheckPointType;
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
     * @param  string $attribute
     * @param  mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->checkPoint->type === CheckPointType::DEFAULT or $this->checkPoint->type ===
            CheckPointType::ARRIVAL) {
            $last_checkpoint = CheckPoint::findOrFail($this->checkPoint->previous_checkpoint_id)->first();
            $last_checkpoint_time = $last_checkpoint->times()->orderBy('created_at', 'desc')->first();
            $last_checkpoint_time_timestamp = Time::findOrFail($last_checkpoint_time->id)->first();
            $date = Carbon::createFromTimeStamp((int)$value);
            $min = Carbon::create(2015, 12, 31, 23, 59, 59);
            $max = Carbon::create(2025, 12, 31, 23, 59, 59);
            return $date->gt($min) && $date->lte($max) && $value >
                $last_checkpoint_time_timestamp->current_time;
        } else {
            $date = Carbon::createFromTimeStamp((int)$value);
            $min = Carbon::create(2015, 12, 31, 23, 59, 59);
            $max = Carbon::create(2025, 12, 31, 23, 59, 59);
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

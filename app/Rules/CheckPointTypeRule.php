<?php

namespace App\Rules;

use App\Enums\CheckPointType;
use Illuminate\Contracts\Validation\Rule;

class CheckPointTypeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return CheckPointType::hasValue($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Bad CheckPoint type for CheckPoint';
    }
}

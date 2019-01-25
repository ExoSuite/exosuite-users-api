<?php

namespace App\Rules;

use App\Enums\Visibility;
use Illuminate\Contracts\Validation\Rule;

class RunVisibilityRule implements Rule
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
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Visibility::hasValue($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Bad visibility type for run.';
    }
}

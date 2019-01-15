<?php

namespace App\Rules;

use App\Enums\LikableEntities;
use Illuminate\Contracts\Validation\Rule;

class ValidateLikeTargetRule implements Rule
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
        return LikableEntities::hasValue($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Bad entity provided.';
    }
}

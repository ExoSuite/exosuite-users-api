<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

const NO_MATCH = 0;

class PasswordRule implements Rule
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
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $value, $matches);
        return sizeof($matches) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans("passwords.bad");
    }
}

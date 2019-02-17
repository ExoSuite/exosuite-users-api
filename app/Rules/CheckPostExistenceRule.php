<?php

namespace App\Rules;

use App\Models\Post;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class CheckPostExistenceRule
 * @package App\Rules
 */
class CheckPostExistenceRule implements Rule
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
        if (Post::whereId($value)->exists()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The provided post id doesn\'t exists.';
    }
}

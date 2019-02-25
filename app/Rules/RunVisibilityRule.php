<?php declare(strict_types = 1);

namespace App\Rules;

use App\Enums\Visibility;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class RunVisibilityRule
 *
 * @package App\Rules
 */
class RunVisibilityRule implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return Visibility::hasValue($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Bad visibility type for run.';
    }
}

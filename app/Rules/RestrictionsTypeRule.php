<?php declare(strict_types = 1);

namespace App\Rules;

use App\Enums\Restriction;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class RestrictionsTypeRule
 *
 * @package App\Rules
 */
class RestrictionsTypeRule implements Rule
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
        return Restriction::hasValue($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Bad restriction type.';
    }
}

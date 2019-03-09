<?php declare(strict_types=1);

namespace App\Rules;

use App\Enums\RequestTypesEnum;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class RequestTypeValidationRule
 *
 * @package App\Rules
 */
class RequestTypeValidationRule implements Rule
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
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return RequestTypesEnum::hasValue($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Unknown request type provided.';
    }
}

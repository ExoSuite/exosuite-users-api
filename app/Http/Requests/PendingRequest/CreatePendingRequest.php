<?php declare(strict_types = 1);

namespace App\Http\Requests\PendingRequest;

use App\Rules\RequestTypeValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreatePendingRequest
 *
 * @package App\Http\Requests\PendingRequest
 */
class CreatePendingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', new RequestTypeValidationRule],
        ];
    }
}

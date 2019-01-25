<?php

namespace App\Http\Requests\PendingRequest;

use App\Rules\RequestTypeValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreatePendingRequest
 * @package App\Http\Requests\PendingRequest
 */
class CreatePendingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', 'string', new RequestTypeValidationRule()],
        ];
    }
}

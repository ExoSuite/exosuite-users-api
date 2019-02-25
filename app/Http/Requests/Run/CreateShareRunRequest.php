<?php declare(strict_types = 1);

namespace App\Http\Requests\Run;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateShareRunRequest
 *
 * @package App\Http\Requests\Run
 */
class CreateShareRunRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|uuid|exists:runs',
            'user_id' => 'sometimes|uuid|exists:users,id',
        ];
    }
}

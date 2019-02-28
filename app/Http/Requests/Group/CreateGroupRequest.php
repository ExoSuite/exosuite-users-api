<?php declare(strict_types = 1);

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateGroupRequest
 *
 * @package App\Http\Requests\Group
 */
class CreateGroupRequest extends FormRequest
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
     * @return mixed[]
     */
    public function rules(): array
    {
        return [
            'users' => 'required|array',
            'users.*' => 'required|distinct|uuid|exists:users,id',
            'name' => 'sometimes|string|max:100',
        ];
    }
}

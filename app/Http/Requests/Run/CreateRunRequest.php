<?php declare(strict_types = 1);

namespace App\Http\Requests\Run;

use App\Rules\RunVisibilityRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateRunRequest
 *
 * @package App\Http\Requests\Run
 */
class CreateRunRequest extends FormRequest
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
            'name' => 'required|string|max:30',
            'description' => 'sometimes|string|max:255',
            'visibility' => ['sometimes', 'string', new RunVisibilityRule],
        ];
    }
}

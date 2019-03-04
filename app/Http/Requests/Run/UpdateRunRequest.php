<?php declare(strict_types = 1);

namespace App\Http\Requests\Run;

use App\Rules\RunVisibilityRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRunRequest
 *
 * @package App\Http\Requests\Run
 */
class UpdateRunRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        /* $run = Run::whereId($this->id());
        TODO : MOVE TO POLICY
        return $run->firstOrFail()->creator_id === Auth::id();*/
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
            'name' => 'sometimes|string|max:30',
            'description' => 'sometimes|string|max:255',
            'visibility' => ['sometimes', 'string', new RunVisibilityRule],
        ];
    }
}

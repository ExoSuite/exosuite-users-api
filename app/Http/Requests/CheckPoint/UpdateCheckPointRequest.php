<?php declare(strict_types = 1);

namespace App\Http\Requests\CheckPoint;

use App\Rules\CheckPointTypeRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCheckPointRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        /*$checkpoint = CheckPoint::whereId($this->id());

        return $checkpoint->firstOrFail()->creator_id === Auth::id();*/
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
            'type' => ['required', 'string', new CheckPointTypeRule],
            'location' => 'required|polygon|max:255',
            'run_id' => 'required|uuid|exists:run,id',
        ];
    }
}

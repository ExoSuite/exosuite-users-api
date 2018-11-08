<?php

namespace App\Http\Requests\Run;

use App\Http\Requests\Abstracts\RouteParamRequestUuidToId;
use App\Models\Run;
use App\Rules\RunVisibilityRule;
use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateRunRequest
 * @package App\Http\Requests\Run
 */
class UpdateRunRequest extends RouteParamRequestUuidToId
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $run = Run::whereId($this->id());
        return $run->firstOrFail()->creator_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:30',
            'description' => 'sometimes|string|max:255',
            'visibility' => ['sometimes', 'string', new RunVisibilityRule()]
        ];
    }
}

<?php

namespace App\Http\Requests\CheckPoint;

use App\Models\CheckPoint;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Abstracts\RouteParamRequestUuidToId;
use Illuminate\Support\Facades\Auth;
use App\Rules\CheckPointTypeRule;


class UpdateCheckPointRequest extends RouteParamRequestUuidToId
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $checkpoint = CheckPoint::whereId($this->id());
        return $checkpoint->firstOrFail()->creator_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', 'string', new CheckPointTypeRule()],
            'location' => 'required|polygon|max:255',
            'run_id' => 'required|uuid|exists:run,id'
        ];
    }
}

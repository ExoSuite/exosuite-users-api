<?php

namespace App\Http\Requests\Time;

use App\Models\Time;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Abstracts\RouteParamRequestUuidToId;
use Illuminate\Support\Facades\Auth;

class UpdateTimeRequest extends RouteParamRequestUuidToId
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $time = Time::whereId($this->id());
        return $time->firstOrFail()->creator_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'run_id' => 'required|uuid|exists:run,id',
            'interval' => 'required|unsignedTinyInteger|max:255',
            'checkpoint_id' => 'required|uuid|exist:check_points,id'
        ];
    }
}

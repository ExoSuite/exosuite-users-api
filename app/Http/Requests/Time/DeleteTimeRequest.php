<?php

namespace App\Http\Requests\Time;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Abstracts\RouteParamRequestUuidToId;
use Illuminate\Support\Facades\Auth;


class DeleteTimeRequest extends RouteParamRequestUuidToId
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
            //
        ];
    }
}

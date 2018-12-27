<?php

namespace App\Http\Requests\Run;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Abstracts\RouteParamRequestUuidToId;
use App\Models\Run;
use Illuminate\Support\Facades\Auth;



class DeleteRunRequest extends RouteParamRequestUuidToId
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
            //
        ];
    }
}

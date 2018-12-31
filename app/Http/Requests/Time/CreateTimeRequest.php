<?php

namespace App\Http\Requests\Time;

use Illuminate\Foundation\Http\FormRequest;

class CreateTimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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

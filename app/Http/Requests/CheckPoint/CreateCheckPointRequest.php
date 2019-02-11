<?php

namespace App\Http\Requests\CheckPoint;

use App\Rules\CheckPointTypeRule;
use App\Rules\PolygonRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCheckPointRequest extends FormRequest
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
            'type' => ['required', 'string', new CheckPointTypeRule()],
            'location' => ['required', new PolygonRule()]
        ];
    }
}

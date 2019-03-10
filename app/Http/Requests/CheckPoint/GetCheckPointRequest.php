<?php declare(strict_types = 1);

namespace App\Http\Requests\CheckPoint;

use App\Http\Requests\Abstracts\RouteParamRequest;

class GetCheckPointRequest extends RouteParamRequest
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
            'id' => 'exists:check_points',
        ];
    }
}

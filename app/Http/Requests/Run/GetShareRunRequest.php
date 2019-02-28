<?php declare(strict_types = 1);

namespace App\Http\Requests\Run;

use App\Http\Requests\Abstracts\RouteParamRequest;

/**
 * Class GetShareRunRequest
 *
 * @package App\Http\Requests\Run
 */
class GetShareRunRequest extends RouteParamRequest
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
            'id' => 'exists:shares',
        ];
    }
}

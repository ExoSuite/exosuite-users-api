<?php declare(strict_types = 1);

namespace App\Http\Requests\Run;

use App\Http\Requests\Abstracts\RouteParamRequest;

/**
 * Class GetRunRequest
 *
 * @package App\Http\Requests\Run
 */
class GetRunRequest extends RouteParamRequest
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
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'id' => 'exists:runs',
        ];
    }
}

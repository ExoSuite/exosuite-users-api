<?php declare(strict_types = 1);

namespace App\Http\Requests\Run;

use App\Http\Requests\Abstracts\RouteParamRequest;

/**
 * Class SearchRunRequest
 *
 * @package App\Http\Requests\Run
 * @property string $text
 */
class SearchRunRequest extends RouteParamRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return mixed[]
     */
    public function rules(): array
    {
        return [
            'text' => 'required|string|max:100|min:1',
        ];
    }
}

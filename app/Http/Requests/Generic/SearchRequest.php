<?php declare(strict_types = 1);

namespace App\Http\Requests\Generic;


use App\Http\Requests\Abstracts\RouteParamRequest;

/**
 * Class SearchRequest
 *
 * @property-read string $text
 * @package App\Http\Requests\Generic
 */
class SearchRequest extends RouteParamRequest
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
            'text' => 'required|string|max:100|min:1',
        ];
    }
}

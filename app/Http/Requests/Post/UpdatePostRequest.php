<?php declare(strict_types = 1);

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePostRequest
 *
 * @package App\Http\Requests\Post
 */
class UpdatePostRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
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
            "content" => 'required|min:1',
        ];
    }
}

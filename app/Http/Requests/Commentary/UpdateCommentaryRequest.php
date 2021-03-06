<?php declare(strict_types = 1);

namespace App\Http\Requests\Commentary;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCommentaryRequest
 *
 * @package App\Http\Requests\Commentary
 */
class UpdateCommentaryRequest extends FormRequest
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
            'content' => 'required|min:1|string|max:2048',
        ];
    }
}

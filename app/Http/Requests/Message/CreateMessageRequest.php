<?php declare(strict_types = 1);

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateMessageRequest
 *
 * @package App\Http\Requests\Message
 */
class CreateMessageRequest extends FormRequest
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
            'contents' => 'required|min:1|string|max:2048',
        ];
    }
}

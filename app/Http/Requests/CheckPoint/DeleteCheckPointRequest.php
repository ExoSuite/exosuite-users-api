<?php declare(strict_types = 1);

namespace App\Http\Requests\CheckPoint;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCheckPointRequest extends FormRequest
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
        return [];
    }
}

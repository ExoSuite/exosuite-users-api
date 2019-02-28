<?php declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateUserProfilePictureCoverRequest
 *
 * @package App\Http\Requests
 */
class CreateUserProfilePictureCoverRequest extends FormRequest
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
            'picture' => 'required|file|mimes:jpeg,png|max:10240',
        ];
    }
}

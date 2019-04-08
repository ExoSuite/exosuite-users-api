<?php declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRestrictionsRequest extends FormRequest
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
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            "city" => "sometimes|string|in:public,private,followers,friends",
            "birthday" => "sometimes|string|in:public,private,followers,friends",
            "description" => "sometimes|string|in:public,private,followers,friends",
            "nomination_preference" => "sometimes|string|in:full_name,nick_name",
        ];
    }
}

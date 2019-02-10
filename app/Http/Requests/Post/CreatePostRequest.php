<?php

namespace App\Http\Requests\Post;

use App\Rules\DashboardRestrictionRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreatePostRequest
 * @package App\Http\Requests\Post
 */
class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "content" => 'required|min:1'
        ];
    }
}

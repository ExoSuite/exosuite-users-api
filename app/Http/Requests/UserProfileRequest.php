<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Middleware\AppendUserId;

class UserProfileRequest extends FormRequest
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
            AppendUserId::$key => 'required|uuid|exists:users,id|unique:user_profiles,id',
            'birthday' => 'sometimes|date_format:YYYY-MM-DD',
            'description' => 'sometimes|string|max:2048',
            'city' => 'sometimes|string|max:100'
        ];
    }

    public function validated()
    {
        $data = parent::validated();
        $data[ 'id' ] = $data[ AppendUserId::$key ];
        unset( $data[ AppendUserId::$key ] );
        return $data;
    }
}

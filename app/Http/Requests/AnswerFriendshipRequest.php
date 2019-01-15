<?php

namespace App\Http\Requests;

use App\Models\PendingRequest;
use Illuminate\Foundation\Http\FormRequest;

class AnswerFriendshipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $request = PendingRequest::whereRequestId($this->get('request_id'))->first();
        if ($request['target_id'] == auth()->user()->id)
            return true;
        else
            return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_id' => 'required|uuid|exists:pending_requests'
        ];
    }
}

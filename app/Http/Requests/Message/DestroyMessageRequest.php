<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class DestroyMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $actual_user_id = Auth::user()->id;
        $message_author_id = $this->get("user_id");
        $group_admins_ids = GroupMember::whereIsAdmin(true)->get();
        if ($actual_user_id === $message_author_id)
            return true;
        foreach ($group_admins_ids as $group_admin_id) {
            if ($actual_user_id === $group_admin_id->user_id)
                return true;
        }
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
            "id" => "required|uuid|exists:messages"
        ];
    }
}

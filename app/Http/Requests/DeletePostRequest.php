<?php

namespace App\Http\Requests;

use App\Models\Dashboard;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class DeletePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $post = Post::whereId($this->get('id'))->first();
        $dashboard = Dashboard::whereId($post['dashboard_id'])->first();
        $owner_id = $dashboard['owner_id'];
        if ($post['author_id'] == auth()->user()->id || auth()->user()->id == $owner_id)
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
            "id" => 'required|uuid|exists:posts'
        ];
    }
}

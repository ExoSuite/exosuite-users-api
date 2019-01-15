<?php

namespace App\Http\Requests;

use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCommentaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $comm = Commentary::whereId($this->get('id'))->first();
        $post = Post::whereId($comm['post_id'])->first();
        $dashboard = Dashboard::whereId($post['dashboard_id'])->first();
        $owner = $dashboard['owner_id'];
        if (auth()->user()->id == $owner
            || auth()->user()->id == $post['author_id']
            || auth()->user()->id == $comm['author_id'])
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
            'id' => 'required|uuid|exists:commentaries'
        ];
    }
}

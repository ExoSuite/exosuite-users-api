<?php

namespace App\Http\Requests;

use App\Models\Dashboard;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class GetCommentariesRequest extends FormRequest
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
        if ($owner_id !== auth()->user()->id)
        {
            switch ($dashboard['restriction'])
            {
                case Restriction::PUBLIC:{
                    return true;
                }
                case Restriction::FRIENDS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return true;
                    else
                        return false;
                }
                case Restriction::FRIENDS_FOLLOWERS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return true;
                    elseif (Follow::whereFollowedId($owner_id)->where('user_id', auth()->user()->id)->exists())
                        return true;
                    else
                        return false;
                }
                default:{
                    return false;
                }
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|uuid|exists:posts'
        ];
    }
}

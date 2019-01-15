<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Dashboard;
use App\Enums\Restriction;
use App\Models\Friendship;
use App\Models\Follow;

class GetPostsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $dashboard = Dashboard::whereId($this->get('dashboard_id'))->first();
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
        }    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "dashboard_id" => 'required|uuid'
        ];
    }
}

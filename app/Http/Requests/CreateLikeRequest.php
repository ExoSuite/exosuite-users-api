<?php

namespace App\Http\Requests;

use App\Enums\LikableEntities;
use App\Models\Commentary;
use App\Models\Post;
use App\Models\Run;
use App\Models\Like;
use App\Rules\ValidateLikeTargetRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateLikeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $target_id = $this->get("liked_id");
        $me = auth()->user()->id;
        switch ($this->get("liked_type")) {
            case LikableEntities::COMMENTARY : {
                if (Commentary::whereId($target_id)->exists()
                    && !Like::whereLikedId($target_id)->whereLikerId($me)->exists())
                    return true;
                else
                    return false;
                break;
            }
            case LikableEntities::POST : {
                if (Post::whereId($target_id)->exists()
                    && !Like::whereLikedId($target_id)->whereLikerId($me)->exists())
                    return true;
                else
                    return false;
                break;
            }
            case LikableEntities::RUN : {
                if (Run::whereId($target_id)->exists()
                    && !Like::whereLikedId($target_id)->whereLikerId($me)->exists())
                    return true;
                else
                    return false;
                break;
            }
            default : {
                return false;
                break;
            }
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
            "liked_id" => "required|uuid",
            "liked_type" => ["required", "string", new ValidateLikeTargetRule()]
        ];
    }
}

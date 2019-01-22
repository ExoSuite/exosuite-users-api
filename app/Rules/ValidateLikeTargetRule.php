<?php

namespace App\Rules;

use App\Enums\LikableEntities;
use App\Models\Commentary;
use App\Models\Post;
use App\Models\Run;
use Illuminate\Contracts\Validation\Rule;

class ValidateLikeTargetRule implements Rule
{
    private $target_type;

    /**
     * Create a new rule instance.
     *
     * @param $target_type
     */
    public function __construct($target_type)
    {
        $this->target_type = $target_type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        switch ($this->target_type) {
            case LikableEntities::COMMENTARY : {
                if (Commentary::whereId($value)->exists())
                    return true;
                else
                    return false;
                break;
            }
            case LikableEntities::POST : {
                if (Post::whereId($value)->exists())
                    return true;
                else
                    return false;
                break;
            }
            case LikableEntities::RUN : {
                if (Run::whereId($value)->exists())
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
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Unknown entity id provided.';
    }
}

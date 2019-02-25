<?php declare(strict_types = 1);

namespace App\Rules;

use App\Enums\LikableEntities;
use App\Models\Commentary;
use App\Models\Post;
use App\Models\Run;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ValidateLikeTargetRule
 * @package App\Rules
 */
class ValidateLikeTargetRule implements Rule
{
    /**
     * @var string
     */
    private $target_type;

    /**
     * Create a new rule instance.
     *
     * @param string $target_type
     */
    public function __construct(string $target_type)
    {
        $this->target_type = $target_type;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes(string $attribute, $value): bool
    {
        switch ($this->target_type) {
            case LikableEntities::COMMENTARY :
                {
                    return Commentary::whereId($value)->exists() ? true : false;

				break;
                }
            case LikableEntities::POST :
                {
                    return Post::whereId($value)->exists() ? true : false;

				break;
                }
            case LikableEntities::RUN :
                {
                    return Run::whereId($value)->exists() ? true : false;

				break;
                }
            default :
                {
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
    public function message(): string
    {
        return 'Unknown entity id provided.';
    }
}

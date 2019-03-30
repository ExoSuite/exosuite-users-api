<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 2019-03-16
 * Time: 23:26
 */

namespace App\Policies\Abstracts;

use App\Enums\Restriction;
use App\Models\Follow;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

abstract class SocialPolicy
{
    use HandlesAuthorization;

    /** @var array<mixed> */
    protected $verifyRelations;

    public function __construct()
    {
        $this->verifyRelations = [
            Restriction::PUBLIC => [$this, 'allowPublic'],
            Restriction::FRIENDS_FOLLOWERS => [$this, 'checkFollow'],
            Restriction::FRIENDS => [$this, 'checkFriendship'],
            Restriction::PRIVATE => [$this, 'denyPrivate'],
        ];
    }


    public function create(User $authenticatedUser, ?Post $post, User $targetedUser): bool
    {
        $owner_id = $targetedUser->id;
        $dashboard = $targetedUser->dashboard;
        $user_id = $authenticatedUser->id;

        if ($post !== null) {
            if ($owner_id === $user_id || $post->author_id === $user_id) {
                return true;
            }

            return call_user_func($this->verifyRelations[$dashboard->writing_restriction], $owner_id, $post);
        }

        if ($owner_id === $user_id) {
            return true;
        }

        return call_user_func($this->verifyRelations[$dashboard->writing_restriction], $owner_id);
    }

    public function index(User $authenticatedUser, ?Post $post, User $targetedUser): bool
    {
        $owner_id = $targetedUser->id;
        $dashboard = $targetedUser->dashboard;
        $user_id = $authenticatedUser->id;

        if ($post !== null) {
            if ($owner_id === $user_id || $post->author_id === $user_id) {
                return true;
            }

            return call_user_func($this->verifyRelations[$dashboard->visibility], $owner_id, $post);
        }

        if ($owner_id === $user_id) {
            return true;
        }

        return call_user_func($this->verifyRelations[$dashboard->visibility], $owner_id);
    }

    public function checkFriendship(string $owner_id): bool
    {
        $user_id = Auth::id();

        return Friendship::whereUserId($owner_id)->whereFriendId($user_id)->exists();
    }

    public function checkFollow(string $owner_id): bool
    {
        $user_id = Auth::id();

        return $this->checkFriendship($owner_id)
            || Follow::whereFollowedId($owner_id)->whereUserId($user_id)->exists();
    }

    public function allowPublic(): bool
    {
        return true;
    }

    public function denyPrivate(): bool
    {
        return false;
    }
}

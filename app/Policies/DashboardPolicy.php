<?php declare(strict_types = 1);

namespace App\Policies;

use App\Enums\Restriction;
use App\Models\Dashboard;
use App\Models\Follow;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class DashboardPolicy
{
    use HandlesAuthorization;

    /** @var array<string<self, string>> */
    public $verifyRelations;

    public function __construct()
    {
        $this->verifyRelations = [
            Restriction::PUBLIC => [$this, 'allowPublic'],
            Restriction::FRIENDS_FOLLOWERS => [$this, 'checkFollow'],
            Restriction::FRIENDS => [$this, 'checkFriendship'],
            Restriction::PRIVATE => [$this, 'denyPrivate'],
        ];
    }

    public function create(User $user, Dashboard $dashboard, ?Post $post = null): bool
    {
        $owner_id = $dashboard->owner_id;

        if ($post !== null) {
            if ($owner_id === Auth::user()->id || $post->author_id === Auth::user()->id) {
                return true;
            }

            return call_user_func($this->verifyRelations[$dashboard->writing_restriction], $owner_id, $post);
        }

        if ($owner_id === Auth::user()->id) {
            return true;
        }

        return call_user_func($this->verifyRelations[$dashboard->writing_restriction], $owner_id);
    }

    public function index(User $user, Dashboard $dashboard, ?Post $post = null): bool
    {
        $owner_id = $dashboard->owner_id;

        if ($post !== null) {
            if ($owner_id === Auth::user()->id || $post->author_id === Auth::user()->id) {
                return true;
            }

            return call_user_func($this->verifyRelations[$dashboard->visibility], $owner_id, $post);
        }

        if ($owner_id === Auth::user()->id) {
            return true;
        }

        return call_user_func($this->verifyRelations[$dashboard->visibility], $owner_id);
    }

    public function checkFriendship(string $owner_id): bool
    {
        return Friendship::whereUserId($owner_id)->whereFriendId(Auth::user()->id)->exists();
    }

    public function checkFollow(string $owner_id): bool
    {
        return Friendship::whereUserId($owner_id)->whereFriendId(Auth::user()->id)->exists()
            || Follow::whereFollowedId($owner_id)->whereUserId(Auth::user()->id)->exists();
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

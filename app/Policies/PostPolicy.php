<?php declare(strict_types = 1);

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Policies\Abstracts\SocialPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy extends SocialPolicy
{
    use HandlesAuthorization;

    public function updatePost(User $authenticatedUser, Post $post): bool
    {
        return $post->author_id === $authenticatedUser->id;
    }

    public function deletePost(User $authenticatedUser, Post $post, User $targetedUser): bool
    {
        return $post->author_id === $authenticatedUser->id
            || $authenticatedUser->id === $targetedUser->dashboard->owner_id;
    }
}

<?php declare(strict_types = 1);

namespace App\Policies;

use App\Models\Commentary;
use App\Models\Post;
use App\Models\User;
use App\Policies\Abstracts\SocialPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class CommentaryPolicy extends SocialPolicy
{
    use HandlesAuthorization;

    public function updateCommentary(User $authenticatedUser, Commentary $commentary): bool
    {
        return $commentary->author_id === $authenticatedUser->id;
    }

    public function deleteCommentary(User $user, Commentary $commentary, Post $post): bool
    {
        $authenticatedUserId = Auth::id();

        return $authenticatedUserId === $user->dashboard->id
            || $authenticatedUserId === $post->author_id
            || $authenticatedUserId === $commentary->author_id;
    }
}

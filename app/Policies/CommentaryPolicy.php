<?php declare(strict_types = 1);

namespace App\Policies;

use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class CommentaryPolicy
{
    use HandlesAuthorization;

    public function updateCommentary(User $user, Commentary $commentary): bool
    {
        return $commentary->author_id === Auth::user()->id;
    }

    public function deleteCommentary(User $user, Commentary $commentary, Dashboard $dashboard, Post $post): bool
    {
        return Auth::user()->id === $dashboard->owner_id
            || Auth::user()->id === $post->author_id
            || Auth::user()->id === $commentary->author_id;
    }
}

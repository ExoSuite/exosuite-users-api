<?php declare(strict_types = 1);

namespace App\Policies;

use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class PostPolicy
{
    use HandlesAuthorization;

    public function updatePost(User $user, Post $post): bool
    {
        return $post->author_id === Auth::user()->id;
    }

    public function deletePost(User $user, Post $post, Dashboard $dashboard): bool
    {
        return $post->author_id === Auth::user()->id || Auth::user()->id === $dashboard->owner_id;
    }
}

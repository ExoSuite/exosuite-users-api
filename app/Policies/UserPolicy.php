<?php declare(strict_types = 1);

namespace App\Policies;

use App\Models\User;
use App\Policies\Abstracts\SocialPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends SocialPolicy
{
    use HandlesAuthorization;

    public function createPost(User $authenticatedUser, User $targetedUser): bool
    {
        return $this->create($authenticatedUser, null, $targetedUser);
    }

    public function getPost(User $authenticatedUser, User $targetedUser): bool
    {
        return $this->index($authenticatedUser, null, $targetedUser);
    }
}

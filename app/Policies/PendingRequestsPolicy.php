<?php declare(strict_types = 1);

namespace App\Policies;

use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PendingRequestsPolicy
{
    use HandlesAuthorization;

    public function answerRequest(User $user, PendingRequest $request): bool
    {
        return $request->target_id === $user->id;
    }
}

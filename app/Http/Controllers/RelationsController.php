<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\RequestTypesEnum;
use App\Http\Controllers\Traits\JsonResponses;
use App\Models\Friendship;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class RelationsController
 *
 * @package App\Http\Controllers
 */
class RelationsController extends Controller
{
    use JsonResponses;

    public function sendFriendshipRequest(User $user): JsonResponse
    {
        /** @var string $authUserId */
        $authUserId = Auth::id();
        $this->createFriendship($user->id, $authUserId);
        $request = PendingRequest::create([
            'requester_id' => $authUserId,
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST,
            'target_id' => $user->id,
        ]);

        return $this->created($request);
    }

    public function createFriendship(string $id, string $authUserId): Friendship
    {
        return Friendship::create([
            'user_id' => $authUserId,
            'friend_id' => $id,
        ]);
    }

    public function acceptRequest(PendingRequest $request): JsonResponse
    {
        /** @var string $authUserId */
        $authUserId = Auth::id();
        $friendship = $this->createFriendship($request->requester_id, $authUserId);
        $request->delete();

        return $this->ok($friendship);
    }

    public function declineRequest(PendingRequest $request): JsonResponse
    {
        $friendship = Friendship::whereFriendId($request->target_id)->whereUserId($request->requester_id);

        if ($friendship->exists()) {
            $friendship->delete();
            $request->delete();

            return $this->noContent();
        }

        $request->delete();

        return $this->noContent();
    }

    public function getMyFriendships(): JsonResponse
    {
        return $this->ok(Auth::user()->friendships("user_id")->get());
    }

    public function getFriendships(User $user): JsonResponse
    {
        return $this->ok($user->friendships("user_id")->get());
    }

    public function deleteFriendships(Friendship $friendship): JsonResponse
    {
        $friendship->delete();

        return $this->noContent();
    }
}

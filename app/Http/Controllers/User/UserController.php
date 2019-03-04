<?php declare(strict_types = 1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserSearchRequest;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Personal
 */
class UserController extends Controller
{

    public function me(): JsonResponse
    {
        return $this->ok(User::with('profile')->whereId(Auth::id())->first());
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        Auth::user()->update($request->validated());

        return $this->noContent();
    }

    public function search(UserSearchRequest $request): JsonResponse
    {
        $users = User::search($request->text)->with('profile')->paginate();

        return $this->ok($users);
    }

    public function groups(): JsonResponse
    {
        return GroupMember::whereUserId(Auth::id())->get()->toJson();
    }
}

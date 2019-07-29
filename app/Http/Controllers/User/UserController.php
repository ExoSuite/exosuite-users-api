<?php declare(strict_types = 1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Generic\SearchRequest;
use App\Http\Requests\User\UpdateUserRequest;
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
    private const USER_SEARCH_PAGE = 15;

    public function me(): JsonResponse
    {
        return $this->ok(Auth::user()->load("profile"));
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        Auth::user()->update($request->validated());

        return $this->noContent();
    }

    public function search(SearchRequest $request): JsonResponse
    {
        return $this->ok(
            User::search($request->text)
                ->with('profile:id,city,description,avatar_id,cover_id')
                ->select(['id', 'first_name', 'last_name', 'nick_name'])
                ->paginate(self::USER_SEARCH_PAGE)
        );
    }

    /**
     * Display a listing of the groups.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function groups(): JsonResponse
    {
        return $this->ok(
            Auth::user()
                ->groups()
                ->latest()
                ->paginate(self::USER_SEARCH_PAGE)
        );
    }
}

<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\Restriction;
use App\Http\Requests\Dashboard\ChangeRestrictionRequest;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

/**
 * Class DashboardsController
 *
 * @package App\Http\Controllers
 */
class DashboardsController extends Controller
{
    public function changeRestriction(ChangeRestrictionRequest $new_policy): JsonResponse
    {
        $new_policy->validated();

        switch ($new_policy->get('restriction')) {
            case Restriction::PUBLIC:
            case Restriction::FRIENDS:
            case Restriction::FRIENDS_FOLLOWERS:
            case Restriction::PRIVATE:
                $dash = Dashboard::whereOwnerId(Auth::user()->id)->first();
                $dash->update(['restriction' => $new_policy->get('restriction')]);

                return $this->ok(['restriction status' => $dash['restriction']]);

            default:
                return Response::json('Wrong restriction type provided.')->setStatusCode(HttpResponse::HTTP_BAD_REQUEST);
        }
    }

    public function getRestriction(): JsonResponse
    {
        return $this->ok(['restriction' => Dashboard::whereOwnerId(Auth::user()->id)->get()->pluck('restriction')]);
    }

    public function getDashboardId(User $user): JsonResponse
    {
        $dash = Dashboard::whereOwnerId($user->id)->first();

        return $this->ok(['dashboard_id' => $dash['id']]);
    }
}

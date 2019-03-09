<?php

namespace App\Http\Controllers;

use App\Enums\Restriction;
use App\Http\Requests\Dashboard\ChangeRestrictionRequest;
use App\Http\Requests\Dashboard\GetDashboardIdRequest;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;


/**
 * Class DashboardsController
 * @package App\Http\Controllers
 */
class DashboardsController extends Controller
{
    /**
     * @param ChangeRestrictionRequest $new_policy
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeRestriction(ChangeRestrictionRequest $new_policy)
    {
        $new_policy->validated();
        $restriction = $new_policy->get('restriction');

        switch ($new_policy->get('restriction_level')) {
            case Restriction::PUBLIC:
            case Restriction::FRIENDS:
            case Restriction::FRIENDS_FOLLOWERS:
            case Restriction::PRIVATE :
                {
                    $dash = Dashboard::whereOwnerId(Auth::user()->id)->first();
                    $dash->update([$restriction => $new_policy->get('restriction_level')]);
                    return $this->ok([$restriction => $dash[$restriction]]);
                    break;
            }
            default :
                {
                    return Response::json('Wrong restriction type provided.')->setStatusCode(HttpResponse::HTTP_BAD_REQUEST);
                    break;
            }
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVisibility()
    {
        return $this->ok(['visibility' => Dashboard::whereOwnerId(Auth::user()->id)->get()->pluck('visibility')]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWritingRestriction()
    {
        return $this->ok(['writing restriction' => Dashboard::whereOwnerId(Auth::user()->id)->get()->pluck('writing_restriction')]);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardId(User $user)
    {
        $dash = Dashboard::whereOwnerId($user->id)->first();
        return $this->ok(['dashboard_id' => $dash['id']]);
    }
}

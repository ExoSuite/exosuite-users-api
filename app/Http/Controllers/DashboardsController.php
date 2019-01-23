<?php

namespace App\Http\Controllers;

use App\Enums\Restriction;
use App\Http\Requests\Dashboard\ChangeRestrictionRequest;
use App\Http\Requests\Dashboard\GetDashboardIdRequest;
use App\Models\Dashboard;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;


class DashboardsController extends Controller
{
    public function changeRestriction(ChangeRestrictionRequest $new_policie)
    {
        $new_policie->validated();

        switch ($new_policie->get('restriction'))
        {
            case Restriction::PUBLIC:
            case Restriction::FRIENDS:
            case Restriction::FRIENDS_FOLLOWERS :
            case Restriction::PRIVATE :
                {
                $dash = Dashboard::whereOwnerId(auth()->user()->id)->first();
                $dash->update(['restriction' => $new_policie->get('restriction')]);
                return $this->ok(['restriction status' => $dash['restriction']]);
                break;
            }
            default :{
                return Response::json('Wrong restriction type provided.')->setStatusCode(HttpResponse::HTTP_BAD_REQUEST);
                break;
            }
        }
    }

    public function getRestriction()
    {
        return $this->ok(['restriction' => Dashboard::whereOwnerId(auth()->user()->id)->get()->pluck('restriction')]);
    }

    public function getDashboardId(GetDashboardIdRequest $request, $user_id)
    {
        $request->validated();
        $dash = Dashboard::whereOwnerId($user_id)->first();
        return $this->ok(['dashboard_id' => $dash['id']]);
    }


}

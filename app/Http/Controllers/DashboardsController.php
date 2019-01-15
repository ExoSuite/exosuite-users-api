<?php

namespace App\Http\Controllers;

use App\Enums\Restriction;
use App\Http\Requests\ChangeRestrictionRequest;
use App\Http\Requests\GetDashboardInfosRequest;
use App\Models\Dashboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;


class DashboardsController extends Controller
{
    private function recupDashboardId(array $data)
    {
        $dash = Dashboard::whereOwnerId($data['id'])->first();
        return ['id' => $dash['id']];
    }

    private function change(array $data)
    {
        return Dashboard::whereOwnerId($data)->first()->get('id');
    }

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

    public function getDashboardId(GetDashboardInfosRequest $request)
    {
        $dash_id = $this->recupDashboardId($request->validated());
        return $this->ok($dash_id);
    }
}

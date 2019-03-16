<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\ChangeRestrictionRequest;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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

        $dash = Dashboard::whereOwnerId(Auth::user()->id)->first();
        $dash->update([$new_policy->get('restriction_field') => $new_policy->get('restriction_level')]);

        return $this->ok([
            $new_policy->get('restriction_field')
            => $dash[$new_policy->get('restriction_field')],
        ]);
    }

    public function getRestriction(): JsonResponse
    {
        return $this->ok([
            'visibility' => Dashboard::whereOwnerId(Auth::user()->id)->get()->pluck('visibility'),
            'write_restriction' => Dashboard::whereOwnerId(Auth::user()->id)->get()->pluck('writing_restriction'),
        ]);
    }

    public function getDashboardId(User $user): JsonResponse
    {
        $dash = Dashboard::whereOwnerId($user->id)->first();

        return $this->ok(['dashboard_id' => $dash['id']]);
    }
}

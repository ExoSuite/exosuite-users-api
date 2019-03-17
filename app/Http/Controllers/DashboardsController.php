<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\ChangeRestrictionRequest;
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
        $dash = Auth::user()->dashboard()->first();
        $restriction_field = $new_policy->get('restriction_field');
        $restriction_level = $new_policy->get('restriction_level');
        $dash->update([$restriction_field => $restriction_level]);

        return $this->ok($dash);
    }

    public function getRestriction(): JsonResponse
    {
        $user = Auth::user();

        return $this->ok($user->dashboard()->first(['writing_restriction', 'visibility']));
    }

    public function getDashboardId(User $user): JsonResponse
    {
        return $this->ok($user->dashboard()->first());
    }
}

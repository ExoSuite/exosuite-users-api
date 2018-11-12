<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 15/10/2018
 * Time: 18:17
 */

namespace App\Services;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Facades\ApiHelper;

/**
 * Class AdministratorServices
 * @package App\Services
 */
class AdministratorServices
{
    /**
     * @param User|Request $data
     * @return boolean|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function handleAuth($data)
    {
        if (App::isLocal() or App::environment("staging")) {
            return true;
        }

        $user = $data instanceof User ? $data : $data->user();
        // if user is authenticated
        if (Auth::check()) {
            return $this->isAdministrator($user);
        }

        return ApiHelper::redirectToLogin();
    }

    private function isAdministrator(User $user): bool
    {
        return $user->inRole(Roles::ADMINISTRATOR);
    }
}

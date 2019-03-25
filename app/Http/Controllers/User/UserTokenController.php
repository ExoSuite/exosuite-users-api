<?php declare(strict_types = 1);

namespace App\Http\Controllers\User;

use App\Enums\TokenScope;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserTokenController extends Controller
{

    public function issuePersonalPictureAccessToken(): JsonResponse
    {
        $token = Auth::user()->createToken('Picture Access Token', [TokenScope::VIEW_PICTURE]);

        return $this->ok(["access_token" => $token->accessToken]);
    }
}

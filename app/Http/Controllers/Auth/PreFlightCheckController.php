<?php declare(strict_types = 1);

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\EmailIsAlreadyRegisteredRequest;
use Illuminate\Http\JsonResponse;

class PreFlightCheckController extends Controller
{

    public function emailIsAlreadyRegistered(EmailIsAlreadyRegisteredRequest $request): JsonResponse
    {
        return $this->noContent();
    }
}

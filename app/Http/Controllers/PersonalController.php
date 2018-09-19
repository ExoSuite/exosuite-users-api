<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

/**
 * Class PersonalController
 * @package App\Http\Controllers\Personal
 */
class PersonalController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->ok(Auth::user());
    }
}

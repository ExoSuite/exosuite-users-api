<?php

namespace App\Http\Controllers\Personal;

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

    public function me()
    {
        return Auth::user()->toJson();
    }
}

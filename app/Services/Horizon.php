<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 15/10/2018
 * Time: 18:17
 */

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Horizon
{
    public function handleAuth(Request $request): bool
    {
        return true;
    }
}

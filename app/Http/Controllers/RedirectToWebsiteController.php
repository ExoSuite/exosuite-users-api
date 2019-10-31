<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Facades\ApiHelper;
use Illuminate\Http\RedirectResponse;

class RedirectToWebsiteController extends Controller
{

    public function redirect(): RedirectResponse
    {
        return ApiHelper::redirectToWebsiteHome();
    }
}

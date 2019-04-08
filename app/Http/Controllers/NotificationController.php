<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use function now;

/**
 * Class NotificationController
 *
 * @package App\Http\Controllers
 */
class NotificationController extends Controller
{

    public const GET_PER_PAGE = 10;

    public function update(?Notification $notification = null): JsonResponse
    {
        if ($notification instanceof Notification) {
            Auth::user()->unreadNotifications()->findOrFail($notification->id)->update(['read_at' => now()]);
        } else {
            Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        }

        return $this->noContent();
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return $this->ok(Auth::user()->notifications()->paginate(self::GET_PER_PAGE));
    }

    public function destroy(?Notification $notification = null): JsonResponse
    {
        if ($notification instanceof Notification) {
            $notifications = Auth::user()->notifications()->findOrFail($notification->id);
            $notifications->delete();
        } else {
            Auth::user()->notifications()->delete();
        }

        return $this->noContent();
    }
}

<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use function now;

/**
 * Class NotificationController
 * @package App\Http\Controllers
 */
class NotificationController extends Controller
{
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
        $user_notifications = Auth::user()->notifications()->get();

        return $this->ok($user_notifications);
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

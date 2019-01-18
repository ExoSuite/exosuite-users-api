<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($notification = null)
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

    /**
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($notification = null)
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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            $notifications = Auth::user()->unreadNotifications()->findOrFail($notification->id);
            $notifications->update(['read_at' => now()]);
        } else {
            $notifications = Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        }
        return $this->ok($notifications);
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
            $notifications = Auth::user()->unreadNotifications()->findOrFail($notification->id);
            $notifications->delete();
        } else {
            Auth::user()->unreadNotifications()->delete();
        }
        return $this->noContent();
    }
}

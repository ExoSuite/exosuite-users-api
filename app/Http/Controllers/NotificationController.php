<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    /**
     * @param UpdateNotificationRequest $request
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        $data = $request->validated();
        $data['read_at'] = Carbon::now();
        $notification->update($data);
        return $this->ok($notification);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function index(User $user)
    {
        $user_notifications = Notification::whereNotifiableId($user->id)->get();
        return $this->ok($user_notifications);
    }

    /**
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return $this->noContent();
    }
}

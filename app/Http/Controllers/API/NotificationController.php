<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Http\Controllers\Controller;
use App\Helpers\ConfigHelper;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->notifications()->latest()->get();
    }

    public function unread(Request $request)
    {
        return $request->user()->unreadNotifications()->get();
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->each->markAsRead();
        return response()->json(['status' => 'ok']);
    }

    public function markRead(Request $request, $id)
    {
        $notification = DatabaseNotification::where('id', $id)
            ->where('notifiable_id', $request->user()->id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    public function getShowInAppNotification()
    {
        $value = ConfigHelper::get('show_in_app_notification', 1);

        return response()->json(['value' => $value]);
    }
}

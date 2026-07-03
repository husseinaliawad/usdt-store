<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(AppNotification::whereNull('user_id')->orWhere('user_id', $request->user()->id)->latest()->paginate(20));
    }

    public function read(Request $request, AppNotification $notification)
    {
        abort_if($notification->user_id && $notification->user_id !== $request->user()->id, 403);
        $notification->update(['read_at' => now()]);
        return response()->json($notification);
    }
}

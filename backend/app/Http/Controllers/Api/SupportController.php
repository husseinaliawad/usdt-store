<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:1000']]);
        AppNotification::create([
            'title' => 'طلب دعم جديد',
            'body' => $request->user()->phone.': '.$data['message'],
        ]);
        return response()->json(['message' => 'تم إرسال طلب الدعم']);
    }
}

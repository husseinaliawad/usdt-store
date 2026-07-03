<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:30'],
            'id_image' => ['required', 'image', 'max:4096'],
            'selfie_image' => ['required', 'image', 'max:4096'],
        ]);

        $kyc = KycVerification::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'id_image_path' => $request->file('id_image')->store('kyc', 'public'),
                'selfie_image_path' => $request->file('selfie_image')->store('kyc', 'public'),
                'status' => 'pending',
            ]
        );
        $request->user()->update(['kyc_status' => 'pending']);

        return response()->json($kyc, 201);
    }
}

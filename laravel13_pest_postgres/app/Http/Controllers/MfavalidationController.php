<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;

class MfavalidationController extends Controller
{
    public function validateOtp(Request $request, ?int $id)
    {
        $otp = $request->input('otp');

        $secret = Cache::remember("user_{$id}_2fa_secret", 600, function () use ($id) {
            $user = User::find($id);
            if ($user && !empty($user->secretkey)) {
                return decrypt($user->secretkey);
            }
            return null;
        });

        if (!$secret) {
            return response()->json(['message' => 'User not found or 2FA not set up.'], 404);
        }

        try {
            $google2fa = new Google2FA();
            
            if ($google2fa->verifyKey($secret, $otp, 1)) {
                return response()->json(['message' => 'OTP Code is successfully validated.'], 200);
            }

            return response()->json(['message' => 'Invalid OTP code, please try again.'], 401);

        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
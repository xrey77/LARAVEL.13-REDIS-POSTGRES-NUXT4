<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Arr;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {

            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            // Use Redis to cache user details for 1 hour (3600 seconds)
            $userData = Cache::remember("user_profile_{$user->id}", 3600, function () use ($user) {
                return [ 
                    'id' => $user->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'username' => $user->username,
                    'isactivated' => $user->isactivated,
                    'isblocked' => $user->isblocked,
                    'mailtoken' => $user->mailtoken,
                    'roles' => $user->roles->pluck('name')->first(),
                    'profilepic' => $user->profilepic,
                    'qrcodeurl' => $user->qrcodeurl,
                ];
            });

            return response()->json(array_merge($userData, [
                'message' => 'Login successful.',
                'token' => $token
            ]), 200);
        }

        return response()->json(['message' => 'Invalid credentials.'], 401);
    }
}

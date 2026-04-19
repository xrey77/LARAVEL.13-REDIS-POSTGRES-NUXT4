<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
 public function register(Request $request) 
 {
    $lockKey = 'register_lock_' . $request->email;
    $lock = Cache::lock($lockKey, 10);

    if (!$lock->get()) {
        return response()->json(['message' => 'Registration in progress.'], 429);
    }

    try {

        if (User::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'Email Address is already taken.'],400);
        }

        if (User::where('username', $request->username)->exists()) {
            return response()->json(['message' => 'Username is already taken.'],400);
        }

        $result = DB::transaction(function () use ($request) {

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname'  => $request->lastname,
                'email'     => $request->email,
                'username'  => $request->username,
                'password'  => Hash::make($request->password),
                'role_id'   => 2
            ]);

            return ['user' => $user, 'status' => 201];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json(['message' => 'You have registered successfully, please login now.'], 201);

    } finally {
        $lock->release();
    }
 }
}
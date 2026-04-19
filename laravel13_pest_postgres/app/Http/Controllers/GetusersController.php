<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // Add this line

class GetusersController extends Controller
{
    public function getAllusers() {
        if (Auth::guard('sanctum')->check()) {
            // Cache users for 1 hour (3600 seconds)
            $users = Cache::remember('all_users', 3600, function () {
                return User::all();
            });

            if ($users->isEmpty()) {
                return response()->json(['message' => 'Users is empty.'], 404);
            }

            return response()->json([
                'message' => 'User Authenticated Successfully.', 
                'user' => $users
            ], 200);
        }

        return response()->json(['message' => 'Un-Authorized Access.'], 401);
    }
}



// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;

// class GetusersController extends Controller
// {
//     public function getAllusers() {
//         if (Auth::guard('sanctum')->check()) {
//             $users = User::all();
//             if ($users->count() == 0) {
//                 return response()->json(['message' => 'Users is empty.'],404);
//             }
//             return response()->json(['message' => 'User Authenticated Successfully.', 'user' => $users],200);
//         } else {
//             return response()->json(['message' => 'Un-Authorized Access.'], 401);
//         }
//     } 
// }

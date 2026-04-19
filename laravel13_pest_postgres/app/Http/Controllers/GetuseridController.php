<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetuseridController extends Controller
{
    public function getUserbydid(string $id) {
        if (Auth::guard('sanctum')->check()) {
            
            // Caches the user for 1 hour (3600 seconds)
            $user = Cache::remember("user_{$id}", 3600, function () use ($id) {
                return User::find($id);
            });

            return response()->json([
                'message' => 'User Authenticated Successfully.',
                'user' => $user
            ], 200);

        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }    
}


// namespace App\Http\Controllers;

// use Illuminate\Support\Facades\Cache;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;

// class GetuseridController extends Controller
// {
//     public function getUserbydid(string $id) {
//         if (Auth::guard('sanctum')->check()) {
//             $user = User::find($id);
//             return response()->json(['message' => 'User Authenticated Successfully.','user' => $user], 200);

//         } else {
//             return response()->json(['message' => 'Un-Authorized Access.'], 401);
//         }
//     }    
// }

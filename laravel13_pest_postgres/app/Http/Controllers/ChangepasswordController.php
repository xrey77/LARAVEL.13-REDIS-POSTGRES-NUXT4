<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Facades\Cache;

class ChangepasswordController extends Controller
{
    public function changeUserpassword(Request $request, ?int $id)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
            $user->save();

            Cache::forget("user_profile_{$id}");

            return response()->json(['message' => 'You change your password successfully.'], 200);
        }

        return response()->json(['message' => 'Un-Authorized Access.'], 401);
    }    
}

// namespace App\Http\Controllers;

// use Illuminate\Support\Facades\Log;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Auth;

// class ChangepasswordController extends Controller
// {
//     public function changeUserpassword(int $id, Request $request)
//     {
//         if (Auth::guard('sanctum')->check()) {
//             $user = User::find($id);
//             $user->password = Hash::make($request->password);
//             $user->save();
//             return response()->json(['message' => 'You change your password successfully....'],200);
//         } else {
//             return response()->json(['message' => 'Un-Authorized Access.'], 401);
//         }
//     }    
// }

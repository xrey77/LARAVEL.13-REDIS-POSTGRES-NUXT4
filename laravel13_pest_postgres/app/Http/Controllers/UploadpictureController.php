<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Laravel\Facades\Image;

class UploadpictureController extends Controller
{
    public function updateProfilepicture(Request $request, ?int $id) 
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found...'], 404);
        }

        if ($request->hasFile('profilepic')) {
            $file = $request->file('profilepic');
            $ext = $file->guessExtension(); 
            $newfile = '00' . $id . '.' . $ext;

            // Image processing
            $img = Image::decode($file);
            $img->resize(100, 100);
        
            $file->move(public_path('users'), $newfile);
            
            // Update Database
            $user->profilepic = "users/" . $newfile;
            $user->save();

            Cache::forget("user_profile_{$id}");
            
            return response()->json(['message' => 'New picture has been uploaded successfully.'], 200);
        }

        return response()->json(['message' => 'Image not found.'], 404);
    }    
}
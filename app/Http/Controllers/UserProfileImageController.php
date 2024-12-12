<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserProfileImage;
use Illuminate\Support\Facades\Auth;

class UserProfileImageController extends Controller
{
    public function store(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Upload the image to 'profile_images' directory in storage
        $imagePath = $request->file('image')->store('profile_images', 'public');

        // Save the image path in the database
        $user = Auth::user();
        $user->profileImage()->updateOrCreate(
            ['user_id' => $user->id],
            ['image_path' => $imagePath]
        );

        // Return the image URL in the response
        return response()->json([
            'status' => true,
            'message' => 'Profile image uploaded successfully.',
            'image_url' => asset('storage/' . $imagePath),
        ], 201);
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $profileImage = $user->profileImage;

        if ($profileImage) {
            return response()->json([
                'status' => true,
                'image_url' => asset('storage/' . $profileImage->image_path),
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Profile image not found.',
        ], 404);
    }
}


<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        // Validate the incoming request
        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|integer|max:11'
        ]);

        // Log the request for debugging
        \Log::info('Register method called', $request->all());

        try {
            // Create a new user
            $user = Users::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id']
            ]);

            // Return success response
            return response()->json([
                'message' => 'User  created successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            // Handle any exceptions and return error response
            return response()->json([
                'error' => 'Something went wrong. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}


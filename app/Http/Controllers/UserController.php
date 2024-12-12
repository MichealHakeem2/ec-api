<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Auth\PasswordResetLinkController as PasswordReset;
use App\Models\PersonalAccessToken;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Get all users.
     *
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        $users = Users::all();
        return response()->json($users);
    }

    /**
     * Get a single user by ID.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = Users::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User   not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $user]);
    }

    /**
     * Register a new user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|integer|max:11'
        ]);

        try {
            $user = Users::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id']
            ]);

            return response()->json([
                'message' => 'User   created successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login a user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
            $user = Auth::user();

            $token = $user->createToken('customer-token')->plainTextToken;



            return response()->json([
                'status' => true,
                'message' => 'Login successful.',
                'users' => $user,
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password.',
        ], 401);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'user' => $request->user(),
        ], 200);
    }

    /**
     * Logout the authenticated user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
// Logout a specific token

// Logout a specific token
public function logout(Request $request): JsonResponse
{
    $token = $request->user()->currentAccessToken();

    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Token not found.',
        ], 404);
    }

    $token->delete();

    return response()->json([
        'status' => true,
        'message' => 'logout successfully.',
    ], 200);
}
// Logout all tokens
public function logoutAll(Request $request): JsonResponse
{
    $request->user()->tokens()->each(function ($token) {
        $token->delete();
    });

    return response()->json([
        'status' => true,
        'message' => 'All tokens deleted successfully.',
    ], 200);
}

    private function getUser($id)
    {
        $user = Users::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User   not found.'], 404);
        }
        return $user;
    }

/**
 * Update a user's details.
 *
 * @param  Request  $request
 * @param  int  $id
 * @return JsonResponse
 */
public function update(Request $request, $id): JsonResponse
{
    $user = $this->getUser($id);

    $validationRules = [
        'username' => 'sometimes|string|max:100',
        'email' => 'sometimes|string|email|max:100|unique:users,email,' . $id,
        'password' => 'sometimes|string|min:8|confirmed',
        'role_id' => 'sometimes|integer|max:11',
        // Add any other fields you want to update here
    ];

    $validatedData = $request->validate($validationRules);

    foreach ($validatedData as $field => $value) {
        if ($field === 'password') {
            $user->password = Hash::make($value);
        } else {
            $user->$field = $value;
        }
    }

    $user->save();

    return response()->json(['status' => true, 'message' => 'User  updated successfully.']);
}

    /**
     * Delete a user.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $user = $this->getUser($id);
        $user->delete();
        return response()->json(['status' => true, 'message' => 'User   deleted successfully.']);
    }

    public function sendPasswordResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['message' => 'Password reset link sent'], 200);
    }

    /**
     * Reset the user password.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    /**
     * Resend email verification link.
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent'], 200);
    }
}


<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterApplicantRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterApplicantRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'applicant',
            'password' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null,
            'email_verified_at' => now(),
        ]);

        AuditLogService::log('user_registered', "New applicant registered: {$user->email}");

        return response()->json([
            'message' => 'Account registered successfully.',
            'user' => new UserResource($user),
        ], 201);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_code !== $request->otp_code) {
            return response()->json(['message' => 'Invalid OTP code.'], 422);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return response()->json(['message' => 'OTP code has expired.'], 422);
        }

        $user->update([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json([
            'message' => 'Account verified successfully.',
            'user' => new UserResource($user),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login credentials.'], 401);
        }

        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            return response()->json(['message' => 'Your account is deactivated.'], 403);
        }

        AuditLogService::log('user_login', "User logged in: {$user->email}");

        return response()->json([
            'message' => 'Login successful.',
            'user' => new UserResource($user),
        ]);
    }

    public function logout(): JsonResponse
    {
        if (Auth::check()) {
            AuditLogService::log('user_logout', "User logged out: " . Auth::user()->email);
            Auth::logout();
        }

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function userProfile(): JsonResponse
    {
        return response()->json([
            'user' => new UserResource(Auth::user()),
        ]);
    }
}

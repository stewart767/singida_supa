<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WebAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginValue = $request->email;
        $loginField = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($loginField, $loginValue)->first();

        if ($user) {
            // Check lockout
            if ($user->is_locked && $user->locked_until && $user->locked_until > now()) {
                $diff = $user->locked_until->diffInMinutes(now()) + 1;
                $msg = "Your account is temporarily locked due to too many failed attempts. Try again in {$diff} minutes.";
                if ($request->wantsJson()) {
                    return response()->json(['message' => $msg], 403);
                }
                return back()->withErrors(['email' => $msg])->withInput();
            }

            // Reset lock if expired
            if ($user->is_locked) {
                $user->update([
                    'is_locked' => false,
                    'locked_until' => null,
                    'failed_login_attempts' => 0
                ]);
            }
        }

        $credentials = [
            $loginField => $loginValue,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Your account has been deactivated.'], 403);
                }
                return back()->withErrors(['email' => 'Your account has been deactivated.'])->withInput();
            }

            // Reset failed attempts on success
            $user->update([
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'locked_until' => null
            ]);

            AuditLogService::log('user_login', "Web login: {$user->email}");

            // Handle forced password change redirect
            if ($user->password_force_change) {
                $redirectUrl = route('profile.edit', ['force' => 1]);
            } else {
                if ($user->isApplicant()) {
                    $flow = $request->input('flow');
                    if ($flow === 'career') {
                        $redirectUrl = route('public.careers.dashboard');
                    } elseif ($flow === 'program') {
                        $redirectUrl = route('applicant.dashboard');
                    } else {
                        if (session()->has('selected_job_id')) {
                            $redirectUrl = route('public.careers.dashboard');
                        } else {
                            $redirectUrl = $user->jobApplications()->exists() 
                                ? route('public.careers.dashboard') 
                                : route('applicant.dashboard');
                        }
                    }
                } else {
                    $redirectUrl = route('admin.dashboard');
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Login successful.',
                    'redirect_url' => $redirectUrl,
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                ]);
            }

            return redirect()->intended($redirectUrl);
        }

        // Increment failed attempts on failure
        if ($user) {
            $attempts = $user->failed_login_attempts + 1;
            $updates = ['failed_login_attempts' => $attempts];

            if ($attempts >= 5) {
                $updates['is_locked'] = true;
                $updates['locked_until'] = now()->addMinutes(15);
                $user->update($updates);

                $msg = 'Too many failed login attempts. Your account has been locked for 15 minutes.';
                if ($request->wantsJson()) {
                    return response()->json(['message' => $msg], 403);
                }
                return back()->withErrors(['email' => $msg])->withInput();
            }

            $user->update($updates);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'The provided credentials do not match our records.'], 422);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->withInput();
    }

    public function register(Request $request)
    {
        $minLength = \App\Models\Setting::get('password_min_length', 8);
        $requireSpecial = \App\Models\Setting::get('password_require_special', false);

        $passwordRules = ['required', 'string', 'min:' . $minLength, 'confirmed'];
        if ($requireSpecial) {
            $passwordRules[] = 'regex:/[!@#$%^&*(),.?":{}|<>]/';
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => $passwordRules,
        ], [
            'password.regex' => 'The password must contain at least one special character.',
        ]);

        $autoActivate = \App\Models\Setting::get('applicant_auto_activate', true);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => 'applicant',
            'is_active' => (bool) $autoActivate,
            'password' => Hash::make($validated['password']),
            'otp_code' => null,
            'otp_expires_at' => null,
            'email_verified_at' => now(),
        ]);

        AuditLogService::log('user_registered', "New applicant registered: {$user->email}");

        // Auto-login since verification is bypassed
        Auth::login($user);
        $request->session()->regenerate();
        
        $flow = $request->input('flow');
        if ($flow === 'career') {
            $redirectUrl = route('public.careers.dashboard');
        } elseif ($flow === 'program') {
            $redirectUrl = route('applicant.dashboard');
        } else {
            $redirectUrl = session()->has('selected_job_id')
                ? route('public.careers.dashboard')
                : route('applicant.dashboard');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Account created successfully!',
                'redirect_url' => $redirectUrl,
                'require_otp' => false,
            ], 201);
        }
        return redirect($redirectUrl);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_code !== $request->otp_code) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Invalid OTP code.'], 422);
            }
            return back()->withErrors(['otp_code' => 'Invalid OTP code.'])->withInput();
        }

        $user->update([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $flow = $request->input('flow');
        if ($flow === 'career') {
            $redirectUrl = route('public.careers.dashboard');
        } elseif ($flow === 'program') {
            $redirectUrl = route('applicant.dashboard');
        } else {
            $redirectUrl = session()->has('selected_job_id')
                ? route('public.careers.dashboard')
                : route('applicant.dashboard');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Account verified successfully.',
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect($redirectUrl);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $isApplicant = $user ? $user->isApplicant() : true;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear guest session
        session()->forget('guest_user_id');

        if ($isApplicant) {
            return redirect()->route('home');
        }

        return redirect()->route('login');
    }
}

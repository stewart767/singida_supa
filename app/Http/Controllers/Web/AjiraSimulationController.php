<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vacancy;
use App\Services\AuditLogService;

class AjiraSimulationController extends Controller
{
    /**
     * Show the simulated Ajira Market Registration Form.
     */
    public function register(Request $request)
    {
        $jobRef = $request->query('job_ref');
        $vacancy = null;
        
        if ($jobRef) {
            $vacancy = Vacancy::where('vacancy_number', $jobRef)->first();
        }

        return view('public.careers.ajira_register', [
            'jobRef' => $jobRef,
            'vacancy' => $vacancy,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Handle the registration submission/callback, link user's account, and redirect to Apply page.
     */
    public function callback(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            $email = $request->input('email');
            $existingUser = \App\Models\User::where('email', $email)->first();

            if ($existingUser) {
                $request->validate([
                    'email' => 'required|email',
                    'password' => 'required|string',
                    'nida_number' => 'required|string|max:30',
                    'job_ref' => 'required|string',
                ]);

                if (!\Illuminate\Support\Facades\Hash::check($request->input('password'), $existingUser->password)) {
                    return back()->withErrors(['password' => 'The password you entered is incorrect.'])->withInput();
                }

                $user = $existingUser;
                $user->update([
                    'ajira_linked' => true,
                ]);

                $applicant = \App\Models\Applicant::firstOrCreate(['user_id' => $user->id]);
                $applicant->update(['nida_number' => $request->input('nida_number')]);

                Auth::login($user);
            } else {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'phone' => 'required|string|max:20|unique:users',
                    'nida_number' => 'required|string|max:30',
                    'password' => 'required|string|min:8|confirmed',
                    'job_ref' => 'required|string',
                ]);

                // Create applicant user
                $user = \App\Models\User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
                    'role' => 'applicant',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'ajira_linked' => true,
                ]);

                // Create Applicant profile reference
                \App\Models\Applicant::create([
                    'user_id' => $user->id,
                    'nida_number' => $request->input('nida_number'),
                ]);

                Auth::login($user);
            }
        } else {
            $request->validate([
                'nida_number' => 'required|string|max:30',
                'job_ref' => 'required|string',
            ]);

            $user->update([
                'ajira_linked' => true,
            ]);

            $applicant = \App\Models\Applicant::firstOrCreate(['user_id' => $user->id]);
            $applicant->update(['nida_number' => $request->input('nida_number')]);
        }

        // Log this action using AuditLogService
        AuditLogService::log(
            'ajira_account_linked',
            "User {$user->email} successfully registered/linked their Ajira Market account.",
            [
                'user_id' => $user->id,
                'nida_number' => $request->input('nida_number'),
            ]
        );

        $jobRef = $request->input('job_ref');

        session(['ajira_linked_success' => true]);

        return redirect()->route('public.careers.apply', ['vacancy_number' => $jobRef])
            ->with('success', 'Your Ajira Market Portal account has been registered and linked successfully! You can now proceed to submit your job application.');
    }
}

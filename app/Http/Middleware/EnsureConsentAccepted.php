<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;
use Symfony\Component\HttpFoundation\Response;

class EnsureConsentAccepted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass consent middleware for career and recruitment-related routes
        if ($request->is('careers*') || $request->is('career*') || $request->routeIs('public.careers.*') || $request->routeIs('careers.*')) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user && $user->isApplicant()) {
            // Automatically ensure an applicant profile exists
            if (!$user->applicant) {
                $user->applicant()->create();
                $user->load('applicant');
            }

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Your account is deactivated.'], 403);
                }
                return redirect()->route('login')->withErrors(['email' => 'Your account is deactivated.']);
            }

            $applicant = $user->applicant;

            // Fetch active versions server-side
            $activePolicy = PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
            $activeTerms = TermsCondition::where('status', 'Published')->latest('effective_date')->first();

            $consentValid = true;

            if ($activePolicy && ($applicant->privacy_policy_version !== $activePolicy->version || $applicant->consent_status !== 'accepted')) {
                $consentValid = false;
            }

            if ($activeTerms && ($applicant->terms_version !== $activeTerms->version || $applicant->consent_status !== 'accepted')) {
                $consentValid = false;
            }

            if (!$consentValid) {
                if ($request->has('programme_id')) {
                    $request->session()->put('selected_programme_id', $request->get('programme_id'));
                }

                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'Consent Required',
                        'redirect_url' => route('applicant.consent.notice'),
                    ], 403);
                }

                return redirect()->route('applicant.consent.notice');
            }
        }

        return $next($request);
    }
}

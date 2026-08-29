<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Payment;
use App\Models\User;
use App\Models\ApplicationActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApplicationVerificationService
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '255') && strlen($phone) > 9) {
            $phone = '0' . substr($phone, 3);
        }
        return $phone;
    }

    public function findApplicationAndUser(string $searchQuery): ?array
    {
        $searchQuery = trim($searchQuery);
        if (empty($searchQuery)) {
            return null;
        }

        // Try searching by control number first
        $payment = Payment::where('control_number', $searchQuery)->first();
        if ($payment && $payment->application) {
            $application = $payment->application;
            if ($application->applicant && $application->applicant->user) {
                return [
                    'application' => $application,
                    'user' => $application->applicant->user
                ];
            }
        }

        // Try searching by application number
        $application = Application::where('application_number', $searchQuery)->first();
        if ($application && $application->applicant && $application->applicant->user) {
            return [
                'application' => $application,
                'user' => $application->applicant->user
            ];
        }

        // Normalize phone number and search
        $normalized = self::normalizePhone($searchQuery);

        $user = User::where(function ($q) use ($searchQuery, $normalized) {
            $q->where('phone', $searchQuery);
            if (!empty($normalized)) {
                $q->orWhere('phone', $normalized);
                if (strlen($normalized) >= 5) {
                    $q->orWhere('phone', 'like', "%{$normalized}")
                      ->orWhere('phone', 'like', "%" . substr($normalized, 1));
                }
            }
        })->first();

        if ($user && $user->applicant) {
            // Find active non-completed application first, otherwise latest
            $application = Application::where('applicant_id', $user->applicant->id)
                ->whereNotIn('status', ['Approved', 'Rejected', 'Expired'])
                ->latest()
                ->first();

            if (!$application) {
                $application = Application::where('applicant_id', $user->applicant->id)->latest()->first();
            }

            if ($application) {
                return [
                    'application' => $application,
                    'user' => $user
                ];
            }
        }

        return null;
    }

    public function sendVerificationOtp(User $user, Application $application): bool
    {
        $otp = (string) rand(100000, 999999);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $maskedPhone = substr($user->phone, 0, 4) . '***' . substr($user->phone, -3);
        $message = "Code yako ya siri ya SUPA ya kuendelea na udahili ni {$otp}. Inatumika kwa dakika 10. (Your SUPA verification code to resume application is {$otp}. Valid for 10 minutes.)";

        $sent = $this->smsService->send($user->phone, $message);

        if ($sent) {
            ApplicationActivity::create([
                'application_id' => $application->id,
                'action' => 'OTP Sent',
                'description' => "Verification OTP sent to registered number ending in " . substr($user->phone, -4),
            ]);
        }

        return $sent;
    }

    public function verifyOtp(User $user, string $otpCode, Application $application): bool
    {
        if (empty($user->otp_code) || empty($otpCode)) {
            return false;
        }

        if ($user->otp_code !== $otpCode) {
            return false;
        }

        if (now()->isAfter($user->otp_expires_at)) {
            return false;
        }

        // Clear OTP
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // Login user
        Auth::login($user);

        ApplicationActivity::create([
            'application_id' => $application->id,
            'action' => 'Application Resumed',
            'description' => 'Applicant verified identity via OTP and resumed application session.',
        ]);

        return true;
    }
}

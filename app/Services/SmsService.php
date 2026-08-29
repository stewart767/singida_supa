<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $phone, string $message): bool
    {
        $enabled = Setting::get('enable_recruitment_sms_notifications', true);

        if (!$enabled) {
            return false;
        }

        // Mock SMS gateway dispatch. In production, connect to a carrier API (like Twilio, Beem, Africa's Talking).
        Log::info("SMS SENT to {$phone}: {$message}");

        return true;
    }
}

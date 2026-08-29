<?php

namespace App\Http\Controllers\Api\Integrations;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SingidaPaymentCallbackController extends Controller
{
    /**
     * Receive payment notifications from Singida after NMB/SARIS callbacks.
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->authenticate($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized callback.',
            ], 401);
        }

        $validated = $request->validate([
            'control_number' => ['required', 'string', 'max:128'],
            'external_reference' => ['nullable', 'string', 'max:100'],
            'external_application_id' => ['nullable', 'integer'],
            'singida_admission_id' => ['nullable', 'integer'],
            'amount' => ['nullable', 'numeric'],
            'receipt' => ['nullable', 'string', 'max:128'],
            'channel' => ['nullable', 'string', 'max:64'],
            'account_number' => ['nullable', 'string', 'max:64'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'payment_status' => ['nullable', 'string', 'max:50'],
            'paid_at' => ['nullable', 'date'],
        ]);

        $payment = Payment::query()
            ->with('application')
            ->where('control_number', $validated['control_number'])
            ->first();

        if (! $payment && ! empty($validated['external_application_id'])) {
            $payment = Payment::query()
                ->with('application')
                ->where('application_id', $validated['external_application_id'])
                ->first();
        }

        if (! $payment && ! empty($validated['external_reference'])) {
            $payment = Payment::query()
                ->with('application')
                ->whereHas('application', function ($q) use ($validated) {
                    $q->where('application_number', $validated['external_reference']);
                })
                ->first();
        }

        if (! $payment) {
            Log::warning('Singida payment callback: payment not found', $validated);

            return response()->json([
                'success' => false,
                'message' => 'Payment record not found on SUPA.',
            ], 404);
        }

        $payment->update([
            'payment_status' => 'paid',
            'payment_method' => 'NMB Bank',
            'transaction_reference' => $validated['receipt'] ?? $payment->transaction_reference,
            'verified_at' => now(),
            'singida_synced' => true,
        ]);

        $application = $payment->application;
        if ($application) {
            $updates = [];
            if (in_array($application->status, ['Draft', 'Pending Payment', 'PAYMENT_PENDING'], true)) {
                $updates['status'] = 'IN_PROGRESS';
                $updates['current_step'] = max($application->current_step, 6);
                $updates['completion_percentage'] = max($application->completion_percentage, 71);
                $updates['last_activity_at'] = now();
            }
            if (! empty($validated['singida_admission_id'])) {
                $updates['singida_admission_id'] = $validated['singida_admission_id'];
            }
            if ($updates) {
                $application->update($updates);
            }

            \App\Models\ApplicationActivity::create([
                'application_id' => $application->id,
                'action' => 'Payment Received',
                'description' => "Singida callback: Payment of TZS " . number_format($payment->amount) . " received and verified.",
            ]);
        }

        Log::info('Singida payment callback processed', [
            'payment_id' => $payment->id,
            'control_number' => $payment->control_number,
            'application_id' => $application?->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated on SUPA.',
            'data' => [
                'payment_id' => $payment->id,
                'application_id' => $application?->id,
                'payment_status' => $payment->payment_status,
            ],
        ]);
    }

    protected function authenticate(Request $request): bool
    {
        $expectedToken = (string) config('services.singida.callback_token');
        $expectedSecret = (string) config('services.singida.callback_secret');

        $providedToken = (string) ($request->bearerToken()
            ?? $request->header('X-Supa-Integration-Token')
            ?? '');

        if ($expectedToken !== '' && ($providedToken === '' || ! hash_equals($expectedToken, $providedToken))) {
            return false;
        }

        if ($expectedSecret !== '') {
            $signature = (string) ($request->header('X-Singida-Signature') ?? '');
            $expected = hash_hmac('sha256', (string) $request->getContent(), $expectedSecret);
            if ($signature === '' || ! hash_equals($expected, $signature)) {
                return false;
            }
        }

        // If neither token nor secret configured, reject in production-like setups.
        if ($expectedToken === '' && $expectedSecret === '') {
            return (bool) config('services.singida.allow_insecure_callback', false);
        }

        return true;
    }
}

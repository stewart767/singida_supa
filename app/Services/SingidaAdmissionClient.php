<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SingidaAdmissionClient
{
    public function isConfigured(): bool
    {
        $base = rtrim((string) config('services.singida.base_url'), '/');
        $token = (string) config('services.singida.api_token');

        return $base !== '' && $token !== '' && (bool) config('services.singida.enabled', true);
    }

    /**
     * Request an NMB control number from Singida and mirror the application there.
     *
     * @return array{control_number: string, amount: float, singida_admission_id: int|null, raw: array}
     */
    public function syncApplication(Application $application): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Singida integration is not configured.');
        }

        $application->loadMissing(['applicant.user', 'programme', 'academicProfile', 'academicYear', 'payment']);

        $applicant = $application->applicant;
        $user = $applicant?->user;
        $profile = $application->academicProfile;

        if (! $user || empty($user->phone)) {
            throw new RuntimeException('Applicant phone number is required before requesting an NMB control number.');
        }

        $amount = (float) ($application->payment?->amount
            ?? $application->programme?->application_fee
            ?? config('services.singida.default_amount', 20000));

        $payload = [
            'external_reference' => $application->application_number,
            'external_application_id' => $application->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'whatsapp_phone' => $applicant->whatsapp_number,
            'gender' => $applicant->gender,
            'date_of_birth' => optional($applicant->date_of_birth)?->toDateString(),
            'region' => $applicant->region,
            'district' => $applicant->district,
            'ward' => $applicant->ward,
            'programme_name' => $application->programme?->name,
            'academic_year' => $application->academicYear?->name ?? (string) date('Y'),
            'amount' => $amount,
            'csee_number' => $profile?->csee_number,
            'csee_year' => $profile?->csee_year,
            'csee_school' => $profile?->csee_school,
            'next_of_kin_name' => $applicant->next_of_kin_name,
            'next_of_kin_phone' => $applicant->next_of_kin_phone,
        ];

        $url = rtrim((string) config('services.singida.base_url'), '/').'/api/integration/supa/admissions';
        $token = (string) config('services.singida.api_token');

        try {
            $response = Http::timeout((int) config('services.singida.timeout', 45))
                ->acceptJson()
                ->withToken($token)
                ->withHeaders([
                    'X-Campus-Api-Token' => $token,
                ])
                ->post($url, $payload)
                ->throw();
        } catch (RequestException $e) {
            $body = $e->response?->json();
            Log::error('SingidaAdmissionClient: sync failed', [
                'application_id' => $application->id,
                'status' => $e->response?->status(),
                'body' => $body,
            ]);

            $message = is_array($body)
                ? ($body['message'] ?? json_encode($body['errors'] ?? $body))
                : $e->getMessage();

            throw new RuntimeException('Failed to get NMB control number from Singida: '.$message, 0, $e);
        }

        $json = $response->json();
        $data = is_array($json['data'] ?? null) ? $json['data'] : [];
        $controlNumber = (string) ($data['control_number'] ?? $data['application_number'] ?? '');

        if ($controlNumber === '') {
            throw new RuntimeException('Singida did not return a control number.');
        }

        return [
            'control_number' => $controlNumber,
            'amount' => (float) ($data['amount'] ?? $amount),
            'singida_admission_id' => isset($data['singida_admission_id']) ? (int) $data['singida_admission_id'] : null,
            'raw' => $data,
        ];
    }

    /**
     * Query Singida API / gateway for real-time payment status by control number.
     *
     * @return array|null
     */
    public function checkPaymentStatus(string $controlNumber, ?string $externalReference = null): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $url = rtrim((string) config('services.singida.base_url'), '/').'/api/integration/supa/admissions';
        $token = (string) config('services.singida.api_token');

        try {
            $response = Http::timeout((int) config('services.singida.timeout', 15))
                ->acceptJson()
                ->withToken($token)
                ->withHeaders([
                    'X-Campus-Api-Token' => $token,
                ])
                ->get($url, [
                    'control_number' => $controlNumber,
                    'external_reference' => $externalReference,
                ]);

            if ($response->successful()) {
                $json = $response->json();
                $data = $json['data'] ?? null;
                if (is_array($data)) {
                    return isset($data[0]) ? $data[0] : null;
                }
                return is_array($json) ? $json : null;
            }
        } catch (\Throwable $e) {
            Log::debug('SingidaAdmissionClient: payment status check skipped/failed: '.$e->getMessage());
        }

        return null;
    }
}

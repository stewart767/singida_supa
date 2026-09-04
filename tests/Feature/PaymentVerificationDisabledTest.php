<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Intake;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\User;
use App\Services\PaymentVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentVerificationDisabledTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_cannot_manually_verify_payment_via_api()
    {
        $admin = User::where('email', 'admin@supa.ac.tz')->first();
        $this->actingAs($admin);

        $payment = Payment::first();
        if (!$payment) {
            $applicant = Applicant::first();
            $programme = Programme::first();
            $academicYear = AcademicYear::first();
            $intake = Intake::first();

            $application = Application::create([
                'application_number' => 'SUPA-2026-999999',
                'applicant_id' => $applicant->id,
                'programme_id' => $programme->id,
                'academic_year_id' => $academicYear->id,
                'intake_id' => $intake->id,
                'admission_type' => 'Diploma',
                'admission_category' => 'Direct Entry',
                'status' => 'Draft',
            ]);

            $payment = Payment::create([
                'application_id' => $application->id,
                'control_number' => '991001234567',
                'amount' => 20000,
                'payment_status' => 'pending',
            ]);
        }

        $response = $this->postJson("/api/v1/admin/payments/{$payment->id}/verify", [
            'status' => 'paid',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Manual payment verification is disabled. Payments are automatically verified via the banking gateway.',
        ]);
    }

    public function test_payment_policy_denies_verify_for_super_admin()
    {
        $admin = User::where('email', 'admin@supa.ac.tz')->first();
        $payment = Payment::first() ?? new Payment();

        $this->assertFalse($admin->can('verify', $payment));
    }

    public function test_payment_verification_service_throws_exception_on_manual_verification()
    {
        $admin = User::where('email', 'admin@supa.ac.tz')->first();
        $payment = Payment::first() ?? new Payment();

        $service = new PaymentVerificationService();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Manual payment verification is disabled');

        $service->verifyPayment($payment, $admin, 'paid');
    }

    public function test_singida_automated_payment_callback_still_verifies_payment()
    {
        $applicant = Applicant::first();
        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $application = Application::create([
            'application_number' => 'SUPA-2026-888888',
            'applicant_id' => $applicant->id,
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'admission_type' => 'Diploma',
            'admission_category' => 'Direct Entry',
            'status' => 'Pending Payment',
        ]);

        $payment = Payment::create([
            'application_id' => $application->id,
            'control_number' => '991009999999',
            'amount' => 20000,
            'payment_status' => 'pending',
        ]);

        config(['services.singida.callback_token' => 'test-callback-token']);

        $response = $this->withHeader('X-Supa-Integration-Token', 'test-callback-token')
            ->postJson('/api/v1/integrations/singida/payment-callback', [
                'control_number' => '991009999999',
                'external_reference' => 'SUPA-2026-888888',
                'amount' => 20000,
                'receipt' => 'NMB-TXN-123456',
                'channel' => 'NMB Bank',
                'payment_status' => 'paid',
            ]);

        $response->assertStatus(200);
        $payment->refresh();
        $this->assertEquals('paid', $payment->payment_status);
        $this->assertEquals('NMB Bank', $payment->payment_method);
        $this->assertEquals('NMB-TXN-123456', $payment->transaction_reference);
    }
}

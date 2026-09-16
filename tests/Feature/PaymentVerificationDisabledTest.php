<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Intake;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\Role;
use App\Models\User;
use App\Services\PaymentVerificationService;
use Illuminate\Auth\Access\AuthorizationException;
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

    public function test_super_admin_can_manually_verify_and_approve_payment_via_api()
    {
        $superAdmin = User::where('email', 'admin@supa.ac.tz')->first();
        if (!$superAdmin) {
            $superAdmin = User::factory()->create(['role' => 'SUPER_ADMIN', 'email' => 'superadmin_test@supa.ac.tz']);
            $saRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
            $superAdmin->roles()->sync([$saRole->id]);
        }

        $this->actingAs($superAdmin);

        $applicant = Applicant::first();
        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $application = Application::create([
            'application_number' => 'SUPA-2026-999999',
            'applicant_id' => $applicant?->id ?? 1,
            'programme_id' => $programme?->id ?? 1,
            'academic_year_id' => $academicYear?->id ?? 1,
            'intake_id' => $intake?->id ?? 1,
            'admission_type' => 'Diploma',
            'admission_category' => 'Direct Entry',
            'status' => 'Pending Payment',
        ]);

        $payment = Payment::create([
            'application_id' => $application->id,
            'control_number' => '991001234567',
            'amount' => 20000,
            'payment_status' => 'pending',
        ]);

        $response = $this->postJson("/api/v1/admin/payments/{$payment->id}/verify", [
            'status' => 'paid',
            'payment_method' => 'NMB Bank',
            'transaction_reference' => 'NMB-MANUAL-123456',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $payment->refresh();
        $this->assertEquals('paid', $payment->payment_status);
        $this->assertEquals($superAdmin->id, $payment->verified_by);
        $this->assertNotNull($payment->verified_at);

        $application->refresh();
        $this->assertEquals('IN_PROGRESS', $application->status);
    }

    public function test_non_superadmin_staff_cannot_manually_verify_payment()
    {
        $financeOfficer = User::where('email', 'finance@supa.ac.tz')->first();
        if (!$financeOfficer) {
            $financeOfficer = User::factory()->create(['role' => 'FINANCE_OFFICER', 'email' => 'finance_test@supa.ac.tz']);
            $foRole = Role::firstOrCreate(['name' => 'finance_officer'], ['display_name' => 'Finance Officer']);
            $financeOfficer->roles()->sync([$foRole->id]);
        }

        $this->actingAs($financeOfficer);

        $applicant = Applicant::first();
        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $application = Application::create([
            'application_number' => 'SUPA-2026-999998',
            'applicant_id' => $applicant?->id ?? 1,
            'programme_id' => $programme?->id ?? 1,
            'academic_year_id' => $academicYear?->id ?? 1,
            'intake_id' => $intake?->id ?? 1,
            'admission_type' => 'Diploma',
            'admission_category' => 'Direct Entry',
            'status' => 'Pending Payment',
        ]);

        $payment = Payment::create([
            'application_id' => $application->id,
            'control_number' => '991001234568',
            'amount' => 20000,
            'payment_status' => 'pending',
        ]);

        $response = $this->postJson("/api/v1/admin/payments/{$payment->id}/verify", [
            'status' => 'paid',
        ]);

        $response->assertStatus(403);
    }

    public function test_payment_policy_grants_verify_exclusively_to_super_admin()
    {
        $superAdmin = User::where('email', 'admin@supa.ac.tz')->first();
        $financeOfficer = User::where('email', 'finance@supa.ac.tz')->first();
        $payment = Payment::first() ?? new Payment();

        $this->assertTrue($superAdmin->can('verify', $payment));

        if ($financeOfficer) {
            $this->assertFalse($financeOfficer->can('verify', $payment));
        }
    }

    public function test_payment_verification_service_denies_non_superadmin()
    {
        $financeOfficer = User::where('email', 'finance@supa.ac.tz')->first();
        if (!$financeOfficer) {
            $financeOfficer = User::factory()->create(['role' => 'FINANCE_OFFICER']);
        }

        $payment = Payment::first() ?? new Payment();
        $service = new PaymentVerificationService();

        $this->expectException(AuthorizationException::class);
        $service->verifyPayment($payment, $financeOfficer, 'paid');
    }

    public function test_singida_automated_payment_callback_still_verifies_payment()
    {
        $applicant = Applicant::first();
        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $application = Application::create([
            'application_number' => 'SUPA-2026-888888',
            'applicant_id' => $applicant?->id ?? 1,
            'programme_id' => $programme?->id ?? 1,
            'academic_year_id' => $academicYear?->id ?? 1,
            'intake_id' => $intake?->id ?? 1,
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

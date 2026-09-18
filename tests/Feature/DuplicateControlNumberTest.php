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
use App\Services\ApplicationWorkflowService;
use App\Services\PaymentVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuplicateControlNumberTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularUser;
    protected Programme $programme;
    protected AcademicYear $academicYear;
    protected Intake $intake;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@singida.ac.tz',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'applicant@gmail.com',
            'role' => 'applicant',
            'is_active' => true,
        ]);

        $this->programme = Programme::first() ?? Programme::create([
            'name' => 'Diploma in Education',
            'code' => 'DPED',
            'department' => 'Education',
            'duration_years' => 2,
            'application_fee' => 20000,
        ]);

        $this->academicYear = AcademicYear::first() ?? AcademicYear::create([
            'name' => '2026/2027',
            'is_active' => true,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]);

        $this->intake = Intake::first() ?? Intake::create([
            'name' => 'September 2026',
            'academic_year_id' => $this->academicYear->id,
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'is_active' => true,
        ]);
    }

    protected function createApplicationWithPayment(string $controlNumber, string $paymentStatus = 'pending'): array
    {
        $user = User::factory()->create();
        $applicant = Applicant::create([
            'user_id' => $user->id,
            'gender' => 'female',
            'date_of_birth' => '2000-05-15',
        ]);

        $appNumber = ApplicationWorkflowService::generateUniqueApplicationNumber();

        $application = Application::create([
            'applicant_id' => $applicant->id,
            'application_number' => $appNumber,
            'programme_id' => $this->programme->id,
            'academic_year_id' => $this->academicYear->id,
            'intake_id' => $this->intake->id,
            'admission_type' => 'Direct Entry',
            'admission_category' => 'Direct Entry',
            'status' => $paymentStatus === 'paid' ? 'IN_PROGRESS' : 'Draft',
        ]);

        $payment = Payment::create([
            'application_id' => $application->id,
            'control_number' => $controlNumber,
            'amount' => 20000,
            'currency' => 'TZS',
            'payment_status' => $paymentStatus,
            'singida_synced' => false,
        ]);

        return [$application, $payment, $user];
    }

    public function test_detects_duplicate_control_numbers_and_summarizes_accurately(): void
    {
        $sharedControl = '991002688888';

        // Student 1: Paid
        [$app1, $payment1] = $this->createApplicationWithPayment($sharedControl, 'paid');

        // Student 2: Unpaid / Pending
        [$app2, $payment2] = $this->createApplicationWithPayment($sharedControl, 'pending');

        $service = app(PaymentVerificationService::class);
        $summary = $service->getDuplicateControlNumbersSummary();

        $this->assertCount(1, $summary);
        $dupGroup = $summary->first();

        $this->assertEquals($sharedControl, $dupGroup['control_number']);
        $this->assertEquals(2, $dupGroup['total_count']);
        $this->assertEquals(1, $dupGroup['paid_count']);
        $this->assertEquals(1, $dupGroup['unpaid_count']);
        $this->assertTrue($dupGroup['has_paid']);
    }

    public function test_superadmin_can_regenerate_control_number_for_unpaid_applicant(): void
    {
        $sharedControl = '991002677777';

        // Student 1: Paid (Keep)
        [$app1, $payment1] = $this->createApplicationWithPayment($sharedControl, 'paid');

        // Student 2: Unpaid (Regenerate)
        [$app2, $payment2] = $this->createApplicationWithPayment($sharedControl, 'pending');

        $response = $this->actingAs($this->superAdmin)->postJson(
            route('admin.payments.regenerate-control-number', $payment2->id),
            [
                'reason' => 'Fixing duplicate control number with paid student',
            ]
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $payment2->refresh();
        $payment1->refresh();

        // Student 2 must now have a NEW control number
        $this->assertNotEquals($sharedControl, $payment2->control_number);
        $this->assertStringStartsWith('99100', $payment2->control_number);
        $this->assertEquals('pending', $payment2->payment_status);

        // Student 1 must retain the ORIGINAL paid control number
        $this->assertEquals($sharedControl, $payment1->control_number);
        $this->assertEquals('paid', $payment1->payment_status);

        // Duplicate detector should now report NO duplicates
        $service = app(PaymentVerificationService::class);
        $this->assertCount(0, $service->getDuplicateControlNumbersSummary());
    }

    public function test_cannot_regenerate_control_number_for_paid_applicant_without_force(): void
    {
        $sharedControl = '991002666666';
        [$app1, $payment1] = $this->createApplicationWithPayment($sharedControl, 'paid');

        $response = $this->actingAs($this->superAdmin)->postJson(
            route('admin.payments.regenerate-control-number', $payment1->id),
            [
                'reason' => 'Attempting to change paid record',
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);

        $payment1->refresh();
        $this->assertEquals($sharedControl, $payment1->control_number);
    }

    public function test_non_superadmin_cannot_regenerate_control_number(): void
    {
        $sharedControl = '991002655555';
        [$app1, $payment1] = $this->createApplicationWithPayment($sharedControl, 'pending');

        $response = $this->actingAs($this->regularUser)->postJson(
            route('admin.payments.regenerate-control-number', $payment1->id),
            [
                'reason' => 'Unauthorized attempt',
            ]
        );

        $response->assertForbidden();
    }

    public function test_superadmin_can_assign_custom_unique_control_number(): void
    {
        $sharedControl = '991002644444';
        $customTarget = '991002699999';

        [$app1, $payment1] = $this->createApplicationWithPayment($sharedControl, 'pending');

        $response = $this->actingAs($this->superAdmin)->postJson(
            route('admin.payments.regenerate-control-number', $payment1->id),
            [
                'custom_control_number' => $customTarget,
                'reason' => 'Assigning custom official NMB control number',
            ]
        );

        $response->assertOk();
        $payment1->refresh();
        $this->assertEquals($customTarget, $payment1->control_number);
    }

    public function test_custom_control_number_rejects_already_existing_duplicate(): void
    {
        $existingControl = '991002633333';
        $pendingControl = '991002622222';

        [$app1, $payment1] = $this->createApplicationWithPayment($existingControl, 'paid');
        [$app2, $payment2] = $this->createApplicationWithPayment($pendingControl, 'pending');

        $response = $this->actingAs($this->superAdmin)->postJson(
            route('admin.payments.regenerate-control-number', $payment2->id),
            [
                'custom_control_number' => $existingControl, // already used by payment 1
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);
    }
}

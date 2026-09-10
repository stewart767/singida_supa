<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Intake;
use App\Models\Programme;
use App\Models\User;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdmissionsReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $activeTerms = \App\Models\TermsCondition::where('status', 'Published')->latest('effective_date')->first();

        Applicant::query()->update([
            'initial_consent_given' => true,
            'consent_status' => 'accepted',
            'consented_at' => now(),
            'privacy_policy_version' => $activePolicy ? $activePolicy->version : '2.1',
            'terms_version' => $activeTerms ? $activeTerms->version : '2.1',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_admissions_report()
    {
        $response = $this->get('/admin/reports/pdf?type=admissions_report');
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_cannot_access_admissions_report()
    {
        $applicantUser = User::where('role', 'applicant')->first();
        $this->actingAs($applicantUser);

        $response = $this->get('/admin/reports/pdf?type=admissions_report');
        $response->assertStatus(403);
    }

    public function test_authorized_admin_can_download_admissions_report()
    {
        $adminUser = User::where('email', 'admin@supa.ac.tz')->first();
        $this->actingAs($adminUser);

        // Make sure there are some records in the db
        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();
        $applicant = Applicant::first();

        // Create an application created today
        $application = Application::create([
            'application_number' => 'APPTEST01',
            'applicant_id' => $applicant->id,
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'status' => 'Under Review',
            'admission_type' => 'direct',
            'admission_category' => 'diploma',
        ]);

        $response = $this->get('/admin/reports/pdf?type=admissions_report');
        $response->assertStatus(200);
        $response->assertViewIs('pdf.admissions-report-pdf');
        $response->assertSee('Admissions Report');
        $response->assertSee($programme->name);
    }

    public function test_admissions_report_filters_by_custom_date_range()
    {
        $adminUser = User::where('email', 'admin@supa.ac.tz')->first();
        $this->actingAs($adminUser);

        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();
        $applicant = Applicant::first();

        // Create an application with a specific creation date
        $application = Application::create([
            'application_number' => 'APPTEST02',
            'applicant_id' => $applicant->id,
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'status' => 'Under Review',
            'admission_type' => 'direct',
            'admission_category' => 'diploma',
        ]);
        $application->created_at = '2026-08-24 10:00:00';
        $application->save();

        // Request with date range matching the application
        $response = $this->get('/admin/reports/pdf?type=admissions_report&start_date=2026-08-24&end_date=2026-08-24');
        $response->assertStatus(200);
        $response->assertSee('Custom (2026-08-24 to 2026-08-24)');
        $response->assertSee('New (Custom Period): <strong>1</strong>', false);

        // Request with date range NOT matching the application
        $response2 = $this->get('/admin/reports/pdf?type=admissions_report&start_date=2026-08-25&end_date=2026-08-25');
        $response2->assertStatus(200);
        $response2->assertSee('Custom (2026-08-25 to 2026-08-25)');
        $response2->assertSee('New (Custom Period): <strong>0</strong>', false);
    }

    public function test_admin_can_store_application_with_location_details()
    {
        $adminUser = User::where('email', 'admin@supa.ac.tz')->first();
        $this->actingAs($adminUser);

        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $response = $this->postJson('/admin/applications', [
            'name' => 'John Tesha',
            'email' => 'john.tesha@test.com',
            'phone' => '+255711999888',
            'gender' => 'male',
            'date_of_birth' => '2000-01-01',
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'admission_type' => 'Diploma',
            'admission_category' => 'Direct Entry',
            'status' => 'Approved',
            'region' => 'Singida',
            'district' => 'Singida Mjini',
            'ward' => 'Ipembe',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('applicants', [
            'region' => 'Singida',
            'district' => 'Singida Mjini',
            'ward' => 'Ipembe',
        ]);
    }

    public function test_admissions_report_does_not_contain_unknown_region_district_or_ward()
    {
        $adminUser = User::where('email', 'admin@supa.ac.tz')->first();
        $this->actingAs($adminUser);

        $response = $this->get('/admin/reports/pdf?type=admissions_report');
        $response->assertStatus(200);
        $response->assertDontSee('UNKNOWN REGION');
        $response->assertDontSee('UNKNOWN DISTRICT');
        $response->assertDontSee('UNKNOWN WARD');
    }

    public function test_applications_pdf_report_respects_search_and_filters()
    {
        $adminUser = User::where('email', 'admin@supa.ac.tz')->first();
        $this->actingAs($adminUser);

        $programme1 = Programme::first();
        $programme2 = Programme::skip(1)->first() ?? Programme::create(['code' => 'TEST02', 'name' => 'Secondary Education', 'department' => 'Education', 'duration_years' => 3, 'tuition_fee' => 1000000]);
        $academicYear = AcademicYear::first();
        $intake = Intake::first();
        $applicant = Applicant::first();

        // Create 2 distinct applications for different programmes
        Application::create([
            'application_number' => 'APP-SPECIFIC-MATCH',
            'applicant_id' => $applicant->id,
            'programme_id' => $programme1->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'status' => 'Under Review',
            'admission_type' => 'Diploma',
            'admission_category' => 'Direct Entry',
        ]);

        Application::create([
            'application_number' => 'APP-OTHER-FILTERED-OUT',
            'applicant_id' => $applicant->id,
            'programme_id' => $programme2->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'status' => 'Under Review',
            'admission_type' => 'Diploma',
            'admission_category' => 'Direct Entry',
        ]);

        // Filter by specific search term
        $response = $this->get('/admin/reports/pdf?type=applications&search=APP-SPECIFIC-MATCH');
        $response->assertStatus(200);
        $response->assertSee('APP-SPECIFIC-MATCH');
        $response->assertDontSee('APP-OTHER-FILTERED-OUT');

        // Filter by programme_id
        $response2 = $this->get('/admin/reports/pdf?type=applications&programme_id=' . $programme1->id);
        $response2->assertStatus(200);
        $response2->assertSee('APP-SPECIFIC-MATCH');
        $response2->assertDontSee('APP-OTHER-FILTERED-OUT');
    }

    public function test_applications_csv_export_respects_filters()
    {
        $adminUser = User::where('email', 'admin@supa.ac.tz')->first();
        $this->actingAs($adminUser);

        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();
        $applicant = Applicant::first();

        Application::create([
            'application_number' => 'APP-CSV-TARGET',
            'applicant_id' => $applicant->id,
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'status' => 'Approved',
            'admission_type' => 'Diploma',
            'admission_category' => 'Direct Entry',
        ]);

        $response = $this->get('/api/v1/admin/export-report?type=applications&search=APP-CSV-TARGET');
        $response->assertStatus(200);
        $this->assertStringContainsString('APP-CSV-TARGET', $response->getContent());
    }
}



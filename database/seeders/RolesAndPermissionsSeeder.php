<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Administrator', 'description' => 'Full administrative access across system.'],
            ['name' => 'registrar', 'display_name' => 'Academic Registrar', 'description' => 'Oversees academic decisions, programmes, and admissions.'],
            ['name' => 'admission_officer', 'display_name' => 'Admission Officer', 'description' => 'Reviews and verifies applicant documents.'],
            ['name' => 'finance_officer', 'display_name' => 'Finance Officer', 'description' => 'Verifies application fee payments and receipts.'],
            ['name' => 'applicant', 'display_name' => 'Student Applicant', 'description' => 'Submits online application and checks status.'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }

        $permissions = [
            ['name' => 'view_dashboard', 'display_name' => 'View Admin Dashboard', 'group' => 'admin'],
            ['name' => 'manage_applications', 'display_name' => 'Manage Applications', 'group' => 'admissions'],
            ['name' => 'verify_documents', 'display_name' => 'Verify Documents', 'group' => 'admissions'],
            ['name' => 'make_admission_decisions', 'display_name' => 'Make Decisions', 'group' => 'admissions'],
            ['name' => 'verify_payments', 'display_name' => 'Verify Payments', 'group' => 'finance'],
            ['name' => 'manage_programmes', 'display_name' => 'Manage Programmes', 'group' => 'academics'],
            ['name' => 'manage_settings', 'display_name' => 'Manage System Settings', 'group' => 'system'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }
    }
}

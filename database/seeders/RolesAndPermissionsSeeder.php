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
            ['name' => 'view_reports', 'display_name' => 'View Reports & Analytics', 'group' => 'system'],
            ['name' => 'download_reports', 'display_name' => 'Download Reports', 'group' => 'system'],
        ];

        $permissionModels = [];
        foreach ($permissions as $p) {
            $permissionModels[$p['name']] = Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        // Sync permissions to roles
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $allPermIds = Permission::pluck('id')->toArray();
            $superAdmin->permissions()->sync($allPermIds);
        }

        $registrar = Role::where('name', 'registrar')->first();
        if ($registrar) {
            $registrarPerms = Permission::whereIn('name', [
                'view_dashboard', 'manage_applications', 'verify_documents',
                'make_admission_decisions', 'manage_programmes', 'view_reports',
                'download_reports'
            ])->pluck('id')->toArray();
            $registrar->permissions()->sync($registrarPerms);
        }

        $admissionOfficer = Role::where('name', 'admission_officer')->first();
        if ($admissionOfficer) {
            $aoPerms = Permission::whereIn('name', [
                'view_dashboard', 'manage_applications', 'verify_documents'
            ])->pluck('id')->toArray();
            $admissionOfficer->permissions()->sync($aoPerms);
        }

        $financeOfficer = Role::where('name', 'finance_officer')->first();
        if ($financeOfficer) {
            $foPerms = Permission::whereIn('name', [
                'view_dashboard', 'verify_payments', 'view_reports', 'download_reports'
            ])->pluck('id')->toArray();
            $financeOfficer->permissions()->sync($foPerms);
        }
    }
}

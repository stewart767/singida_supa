<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class RecruitmentRolesAndSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        $roles = [
            ['name' => 'hr_director', 'display_name' => 'HR Director', 'description' => 'Director of Human Resources'],
            ['name' => 'hr_manager', 'display_name' => 'HR Manager', 'description' => 'Human Resources Manager'],
            ['name' => 'hr_officer', 'display_name' => 'HR Officer', 'description' => 'Human Resources Officer'],
            ['name' => 'interview_panel', 'display_name' => 'Interview Panel', 'description' => 'Member of Interview Panel'],
            ['name' => 'designation_head', 'display_name' => 'Designation Head', 'description' => 'Head of Designation'],
        ];

        $roleModels = [];
        foreach ($roles as $r) {
            $roleModels[$r['name']] = Role::firstOrCreate(['name' => $r['name']], $r);
        }

        // Retrieve existing roles
        $superAdmin = Role::where('name', 'super_admin')->first();

        // 2. Seed Permissions
        $permissions = [
            ['name' => 'view_recruitment_dashboard', 'display_name' => 'View Recruitment Dashboard', 'group' => 'recruitment'],
            ['name' => 'manage_job_categories', 'display_name' => 'Manage Job Categories', 'group' => 'recruitment'],
            ['name' => 'manage_designations', 'display_name' => 'Manage Designations', 'group' => 'recruitment'],
            ['name' => 'manage_positions', 'display_name' => 'Manage Positions', 'group' => 'recruitment'],
            ['name' => 'manage_vacancies', 'display_name' => 'Manage Vacancies', 'group' => 'recruitment'],
            ['name' => 'manage_job_applications', 'display_name' => 'Manage Job Applications', 'group' => 'recruitment'],
            ['name' => 'shortlist_applicants', 'display_name' => 'Shortlist Applicants', 'group' => 'recruitment'],
            ['name' => 'schedule_interviews', 'display_name' => 'Schedule Interviews', 'group' => 'recruitment'],
            ['name' => 'score_interviews', 'display_name' => 'Score Interviews', 'group' => 'recruitment'],
            ['name' => 'evaluate_applicants', 'display_name' => 'Final Evaluations', 'group' => 'recruitment'],
            ['name' => 'generate_offer_letters', 'display_name' => 'Generate Offer Letters', 'group' => 'recruitment'],
            ['name' => 'manage_talent_pool', 'display_name' => 'Manage Talent Pool', 'group' => 'recruitment'],
            ['name' => 'view_recruitment_reports', 'display_name' => 'View Recruitment Reports', 'group' => 'recruitment'],
            ['name' => 'manage_recruitment_settings', 'display_name' => 'Manage Recruitment Settings', 'group' => 'recruitment'],
        ];

        $permissionModels = [];
        foreach ($permissions as $p) {
            $permissionModels[$p['name']] = Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        // 3. Bind Permissions to Roles
        // Super Admin gets all new permissions
        if ($superAdmin) {
            foreach ($permissionModels as $pm) {
                $superAdmin->permissions()->syncWithoutDetaching([$pm->id]);
            }
        }

        // HR Director permissions
        if (isset($roleModels['hr_director'])) {
            $roleModels['hr_director']->permissions()->sync(array_values(array_map(fn($p) => $p->id, $permissionModels)));
        }

        // HR Manager permissions (all except managing settings)
        if (isset($roleModels['hr_manager'])) {
            $managerPerms = collect($permissionModels)->except(['manage_recruitment_settings'])->map(fn($p) => $p->id)->all();
            $roleModels['hr_manager']->permissions()->sync($managerPerms);
        }

        // HR Officer permissions
        if (isset($roleModels['hr_officer'])) {
            $officerPerms = collect($permissionModels)->only([
                'view_recruitment_dashboard',
                'manage_job_categories',
                'manage_designations',
                'manage_positions',
                'manage_vacancies',
                'manage_job_applications',
                'shortlist_applicants',
                'schedule_interviews',
                'score_interviews',
                'manage_talent_pool'
            ])->map(fn($p) => $p->id)->all();
            $roleModels['hr_officer']->permissions()->sync($officerPerms);
        }

        // Interview Panel permissions
        if (isset($roleModels['interview_panel'])) {
            $panelPerms = collect($permissionModels)->only(['score_interviews'])->map(fn($p) => $p->id)->all();
            $roleModels['interview_panel']->permissions()->sync($panelPerms);
        }

        // Designation Head permissions
        if (isset($roleModels['designation_head'])) {
            $desigHeadPerms = collect($permissionModels)->only([
                'view_recruitment_dashboard',
                'manage_vacancies',
                'manage_job_applications',
                'score_interviews'
            ])->map(fn($p) => $p->id)->all();
            $roleModels['designation_head']->permissions()->sync($desigHeadPerms);
        }

        // 4. Seed Settings
        $settings = [
            ['key' => 'enable_recruitment_module', 'value' => '1', 'group' => 'recruitment', 'type' => 'boolean', 'description' => 'Enable or disable the entire recruitment module'],
            ['key' => 'enable_public_career_portal', 'value' => '1', 'group' => 'recruitment', 'type' => 'boolean', 'description' => 'Enable public career site for applicants'],
            ['key' => 'enable_recruitment_email_notifications', 'value' => '1', 'group' => 'recruitment', 'type' => 'boolean', 'description' => 'Send email notifications automatically for recruitment updates'],
            ['key' => 'enable_recruitment_sms_notifications', 'value' => '1', 'group' => 'recruitment', 'type' => 'boolean', 'description' => 'Send SMS alerts for recruitment stages'],
            ['key' => 'enable_online_applications', 'value' => '1', 'group' => 'recruitment', 'type' => 'boolean', 'description' => 'Allow online applications from public applicants'],
            ['key' => 'enable_interview_scheduling', 'value' => '1', 'group' => 'recruitment', 'type' => 'boolean', 'description' => 'Enable interview scheduler and scorecard templates'],
            ['key' => 'enable_offer_letter_generation', 'value' => '1', 'group' => 'recruitment', 'type' => 'boolean', 'description' => 'Generate and send PDF offer letters with digital signature support'],
            ['key' => 'enable_talent_pool', 'value' => '1', 'group' => 'recruitment', 'type' => 'boolean', 'description' => 'Retain rejected but qualified applicants in the talent database'],
        ];

        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}

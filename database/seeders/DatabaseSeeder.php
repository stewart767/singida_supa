<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AcademicYearAndIntakeSeeder::class,
            ProgrammeSeeder::class,
            SettingSeeder::class,
            CmsSeeder::class,
            UserAndApplicantSeeder::class,
            RecruitmentRolesAndSettingsSeeder::class,
            RecruitmentDataSeeder::class,
            ConsentPolicySeeder::class,
        ]);
    }
}

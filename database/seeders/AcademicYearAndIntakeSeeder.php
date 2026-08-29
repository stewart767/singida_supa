<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Intake;
use Illuminate\Database\Seeder;

class AcademicYearAndIntakeSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::firstOrCreate(
            ['code' => '2026/2027'],
            [
                'name' => 'Academic Year 2026/2027',
                'start_date' => '2026-09-01',
                'end_date' => '2027-06-30',
                'application_deadline' => '2026-10-31',
                'is_current' => true,
                'is_active' => true,
            ]
        );

        $intakes = [
            ['name' => 'September Intake', 'code' => 'SEP2026', 'description' => 'Main Academic Intake'],
            ['name' => 'January Intake', 'code' => 'JAN2027', 'description' => 'Mid-Year Intake'],
            ['name' => 'March Intake', 'code' => 'MAR2027', 'description' => 'Executive & Foundation Intake'],
        ];

        foreach ($intakes as $i) {
            Intake::firstOrCreate(['code' => $i['code']], $i);
        }
    }
}

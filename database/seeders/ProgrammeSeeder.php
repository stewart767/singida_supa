<?php

namespace Database\Seeders;

use App\Models\Programme;
use Illuminate\Database\Seeder;

class ProgrammeSeeder extends Seeder
{
    public function run(): void
    {
        $programmes = [
            [
                'code' => 'BAED',
                'name' => 'Bachelor of Arts with Education',
                'department' => 'Department of Educational Studies',
                'faculty' => 'Faculty of Education',
                'description' => 'A comprehensive degree equipping future educators with pedagogical and subject mastery.',
                'entry_requirements' => 'Diploma GPA 3.0+ / Form VI',
                'duration_years' => 3,
                'annual_fee' => 1200000.00,
                'monthly_fee' => 120000.00,
                'application_fee' => 20000.00,
                'is_active' => true,
            ],
            [
                'code' => 'BSCED',
                'name' => 'Bachelor of Science with Education',
                'department' => 'Department of Science & Mathematics Education',
                'faculty' => 'Faculty of Science',
                'description' => 'Designed for science educators specializing in Physics, Chemistry, Biology, and Mathematics.',
                'entry_requirements' => 'Diploma GPA 3.0+ / Form VI',
                'duration_years' => 3,
                'annual_fee' => 1500000.00,
                'monthly_fee' => 150000.00,
                'application_fee' => 20000.00,
                'is_active' => true,
            ],
            [
                'code' => 'IMPTE',
                'name' => 'International Master of Pedagogy & Technology',
                'department' => 'Department of Educational Technology',
                'faculty' => 'Faculty of Post-Graduate Studies',
                'description' => 'Advanced postgraduate degree integrating cutting-edge educational technology and instructional design.',
                'entry_requirements' => 'Diploma GPA 3.0+; Shahada + Uzamili',
                'duration_years' => 2,
                'annual_fee' => 2800000.00,
                'monthly_fee' => 280000.00,
                'application_fee' => 20000.00,
                'is_active' => true,
            ],
            [
                'code' => 'Foundation',
                'name' => 'Foundation Course for Higher Education',
                'department' => 'Center for Open & Distance Learning',
                'faculty' => 'Faculty of Continuing Education',
                'description' => 'Bridging programme designed for applicants with Diploma GPA 2.0-2.9 to qualify for degree admission.',
                'entry_requirements' => 'Diploma ya Ualimu GPA 2.0–2.9',
                'duration_years' => 1,
                'annual_fee' => 900000.00,
                'monthly_fee' => 90000.00,
                'application_fee' => 20000.00,
                'is_active' => true,
            ],
        ];

        foreach ($programmes as $p) {
            Programme::updateOrCreate(['code' => $p['code']], $p);
        }
    }
}

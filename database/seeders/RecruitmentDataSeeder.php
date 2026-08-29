<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Campus;
use App\Models\JobCategory;
use App\Models\Designation;
use App\Models\Position;
use App\Models\Vacancy;
use App\Models\JobApplication;
use App\Models\JobApplicationStage;
use App\Models\Interview;
use App\Models\InterviewScorecard;
use App\Models\WrittenTest;
use App\Models\OfferLetter;
use App\Models\TalentPool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecruitmentDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Campuses
        $campusSingida = Campus::firstOrCreate(['code' => 'SINGIDA'], [
            'name' => 'Singida Main Campus',
            'location' => 'Singida',
            'status' => 'active',
        ]);
        
        $campusDSM = Campus::firstOrCreate(['code' => 'DSM'], [
            'name' => 'Dar es Salaam Suboffice',
            'location' => 'Dar es Salaam',
            'status' => 'active',
        ]);

        $campusDodoma = Campus::firstOrCreate(['code' => 'DOM'], [
            'name' => 'Dodoma Campus',
            'location' => 'Dodoma',
            'status' => 'active',
        ]);

        // 2. Create Job Categories
        $catIT = JobCategory::firstOrCreate(['name' => 'IT & Engineering'], [
            'description' => 'Software engineering, database admin, and system admin positions.',
            'status' => 'active',
            'display_order' => 1,
        ]);
        
        $catAcad = JobCategory::firstOrCreate(['name' => 'Academic Staff'], [
            'description' => 'Lecturers, assistant lecturers, professors, and academic tutors.',
            'status' => 'active',
            'display_order' => 2,
        ]);

        $catAdmin = JobCategory::firstOrCreate(['name' => 'Administrative Staff'], [
            'description' => 'Registrars, HR officers, finance control officers, and assistants.',
            'status' => 'active',
            'display_order' => 3,
        ]);

        // 3. Create Designations
        $desigCS = Designation::firstOrCreate(['short_code' => 'CS'], [
            'name' => 'Computer Science & IT',
            'description' => 'Academic and engineering systems designation.',
            'status' => 'active',
        ]);

        $desigEdu = Designation::firstOrCreate(['short_code' => 'EDU'], [
            'name' => 'Education',
            'description' => 'Teacher training and research programs.',
            'status' => 'active',
        ]);

        $desigFin = Designation::firstOrCreate(['short_code' => 'FIN'], [
            'name' => 'Finance & Accounting',
            'description' => 'University treasury and student payments verification.',
            'status' => 'active',
        ]);

        // 4. Create Positions
        $posDev = Position::firstOrCreate(['name' => 'Senior Laravel Developer'], [
            'designation_id' => $desigCS->id,
            'job_category_id' => $catIT->id,
            'campus_id' => $campusSingida->id,
            'employment_type' => 'Full-time',
            'salary_grade' => 'PG 8',
            'status' => 'active',
        ]);

        $posLec = Position::firstOrCreate(['name' => 'Assistant Lecturer in ICT'], [
            'designation_id' => $desigCS->id,
            'job_category_id' => $catAcad->id,
            'campus_id' => $campusSingida->id,
            'employment_type' => 'Full-time',
            'salary_grade' => 'PG 9',
            'status' => 'active',
        ]);

        $posAcc = Position::firstOrCreate(['name' => 'Accounts Officer'], [
            'designation_id' => $desigFin->id,
            'job_category_id' => $catAdmin->id,
            'campus_id' => $campusDSM->id,
            'employment_type' => 'Full-time',
            'salary_grade' => 'PG 6',
            'status' => 'active',
        ]);

        // 5. Setup JSON configurations for vacancy requirements
        $defaultRequirements = [
            'mandatory_documents' => ['cv', 'cover_letter', 'academic_certificates', 'academic_transcripts', 'birth_certificate', 'nida', 'passport_photo'],
            'optional_documents' => ['tin', 'nssf', 'professional_membership', 'recommendation_letter'],
            'min_referees' => 3,
            'experience_required' => [
                'min_years' => 2,
            ],
            'qualifications_required' => ['Degree', 'CPA', 'Masters'],
            'ict_skills_checklist' => [
                'Microsoft Word', 'Excel', 'PowerPoint', 'Google Workspace', 'Internet Research', 'Email Communication'
            ],
        ];

        // 6. Create Vacancies
        $vacDev = Vacancy::firstOrCreate(['vacancy_number' => 'VAC-2026-001'], [
            'job_title' => 'Senior Laravel Developer',
            'department_name' => 'ICT Department',
            'designation_id' => $desigCS->id,
            'position_id' => $posDev->id,
            'job_category_id' => $catIT->id,
            'campus_id' => $campusSingida->id,
            'number_of_positions' => 2,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida Main Campus',
            'recommended_region' => 'Singida',
            'salary_range' => 'TZS 2,500,000 - 3,500,000',
            'application_deadline' => now()->addDays(20),
            'closing_date' => now()->addDays(22),
            'responsibilities' => "Develop and maintain portal dashboards.\nIntegrate RESTful application interfaces.\nOptimize database models.",
            'qualifications' => "Bachelor's degree in Software Engineering, Computer Science or related field.",
            'required_experience' => "3+ years of building web apps using Laravel framework.",
            'required_skills' => "PHP, Laravel, Tailwind CSS, Alpine.js, SQLite.",
            'benefits' => "Complete institutional health coverage, monthly commuter allowance.",
            'status' => 'Published',
            'requirements' => $defaultRequirements,
        ]);

        $vacLec = Vacancy::firstOrCreate(['vacancy_number' => 'VAC-2026-002'], [
            'job_title' => 'Assistant Lecturer in ICT',
            'department_name' => 'Academic Department',
            'designation_id' => $desigCS->id,
            'position_id' => $posLec->id,
            'job_category_id' => $catAcad->id,
            'campus_id' => $campusSingida->id,
            'number_of_positions' => 3,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida Main Campus',
            'recommended_region' => 'Singida',
            'salary_range' => 'TZS 3,000,000 - 4,200,000',
            'application_deadline' => now()->addDays(15),
            'closing_date' => now()->addDays(17),
            'responsibilities' => "Lecture undergraduate classes on Web Technologies.\nSupervise final year student IT projects.\nPerform academic research.",
            'qualifications' => "Master's degree in Information Technology, Computer Science or equivalent.",
            'required_experience' => "Minimum of 2 years tutoring or lecturing experience.",
            'required_skills' => "Classroom management, Python, Java, web curriculum development.",
            'benefits' => "Academic research grant allowance, housing scheme eligibility.",
            'status' => 'Published',
            'requirements' => array_merge($defaultRequirements, ['qualifications_required' => ['Masters', 'PhD']]),
        ]);

        $vacAcc = Vacancy::firstOrCreate(['vacancy_number' => 'VAC-2026-003'], [
            'job_title' => 'Accounts Officer',
            'department_name' => 'Finance Department',
            'designation_id' => $desigFin->id,
            'position_id' => $posAcc->id,
            'job_category_id' => $catAdmin->id,
            'campus_id' => $campusDSM->id,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Contract',
            'location' => 'Dar es Salaam Suboffice',
            'recommended_region' => 'Dar es Salaam',
            'salary_range' => 'TZS 1,200,000 - 1,800,000',
            'application_deadline' => now()->addDays(30),
            'closing_date' => now()->addDays(35),
            'responsibilities' => "Verify student tuition fee receipts.\nGenerate daily bank statement match reports.\nReconcile petty cash logs.",
            'qualifications' => "Advanced Diploma in Accountancy or Bachelor of Commerce in Finance.",
            'required_experience' => "1 year post-graduate internship or work experience in auditing.",
            'required_skills' => "Microsoft Excel, QuickBooks, general ledger entries.",
            'benefits' => "Performance bonuses, medical coverage.",
            'status' => 'Published',
            'requirements' => array_merge($defaultRequirements, ['qualifications_required' => ['Degree', 'CPA']]),
        ]);

        // 7. Retrieve Staff Users for Panel Members & HR Officer
        $hrManager = User::whereHas('roles', fn($q) => $q->where('name', 'hr_manager'))->first();
        if (!$hrManager) {
            $hrManager = User::first(); // Fallback
        }

        $applicantRole = Role::where('name', 'applicant')->first();

        // 8. Seed Sample Applicants at different stages
        $candidates = [
            [
                'name' => 'Juma Hamisi',
                'email' => 'juma.hamisi@example.com',
                'status' => 'Applied',
                'step' => 11,
                'vacancy' => $vacDev,
            ],
            [
                'name' => 'Mary Mwangi',
                'email' => 'mary.mwangi@example.com',
                'status' => 'Under Review',
                'step' => 11,
                'vacancy' => $vacDev,
            ],
            [
                'name' => 'Agnes Shayo',
                'email' => 'agnes.shayo@example.com',
                'status' => 'Shortlisted',
                'step' => 11,
                'vacancy' => $vacLec,
            ],
            [
                'name' => 'Daniel Kweka',
                'email' => 'daniel.kweka@example.com',
                'status' => 'Interview',
                'step' => 11,
                'vacancy' => $vacDev,
                'interview' => true,
            ],
            [
                'name' => 'Beatrice Mboya',
                'email' => 'beatrice.mboya@example.com',
                'status' => 'Written Test',
                'step' => 11,
                'vacancy' => $vacDev,
                'test' => true,
            ],
            [
                'name' => 'George Temu',
                'email' => 'george.temu@example.com',
                'status' => 'Selected',
                'step' => 11,
                'vacancy' => $vacLec,
                'interview' => true,
                'scorecard' => true,
            ],
            [
                'name' => 'Faraja Macha',
                'email' => 'faraja.macha@example.com',
                'status' => 'Offer Letter',
                'step' => 11,
                'vacancy' => $vacDev,
                'offer' => 'Sent',
            ],
            [
                'name' => 'Halima Rashid',
                'email' => 'halima.rashid@example.com',
                'status' => 'Hired',
                'step' => 11,
                'vacancy' => $vacDev,
                'offer' => 'Accepted',
            ],
            [
                'name' => 'Edward Lowassa',
                'email' => 'edward.lowassa@example.com',
                'status' => 'Rejected',
                'step' => 11,
                'vacancy' => $vacAcc,
                'talent_pool' => true,
            ],
            // A draft application where applicant hasn't submitted yet
            [
                'name' => 'Salma Yusuf',
                'email' => 'salma.yusuf@example.com',
                'status' => 'Applied',
                'step' => 5, // Draft at Step 5
                'vacancy' => $vacDev,
                'submitted' => false,
            ]
        ];

        $appIndex = 1;
        foreach ($candidates as $cData) {
            $user = User::firstOrCreate(['email' => $cData['email']], [
                'name' => $cData['name'],
                'password' => bcrypt('password'),
                'phone' => '+2557' . rand(10000000, 99999999),
                'role' => 'applicant',
            ]);
            if ($applicantRole) {
                $user->roles()->syncWithoutDetaching([$applicantRole->id]);
            }

            // Create Application with 11 steps structure
            $appNumber = 'SUPA-JOB-' . date('Y') . '-' . str_pad((string)$appIndex++, 6, '0', STR_PAD_LEFT);
            
            $educationHistory = [
                [
                    'institution' => 'University of Dar es Salaam',
                    'level' => 'Bachelor',
                    'award' => 'Bachelor of Science',
                    'programme' => 'Computer Science',
                    'start_year' => '2016',
                    'completion_year' => '2019',
                    'gpa_grade' => '3.8',
                    'certificate_path' => 'job_documents/mock_cert.pdf',
                ]
            ];

            $experienceHistory = [
                [
                    'employer' => 'Local Software Tech Ltd',
                    'position' => 'Junior Developer',
                    'start_year' => '2019',
                    'end_year' => '2023',
                    'responsibilities' => 'PHP backend code development.',
                ]
            ];

            $ictSkills = [
                ['skill' => 'Microsoft Word', 'level' => 'Advanced'],
                ['skill' => 'Excel', 'level' => 'Advanced'],
                ['skill' => 'Google Workspace', 'level' => 'Expert'],
            ];

            $referees = [
                ['name' => 'Dr. Jane Smith', 'organization' => 'UDSM', 'position' => 'Lecturer', 'phone' => '+255711122233', 'email' => 'smith@udsm.ac.tz'],
                ['name' => 'Prof. Peter Elias', 'organization' => 'DIT', 'position' => 'Professor', 'phone' => '+255722233344', 'email' => 'elias@dit.ac.tz'],
                ['name' => 'Eng. Ally Kassim', 'organization' => 'VODA', 'position' => 'Senior Developer', 'phone' => '+255733344455', 'email' => 'ally@voda.co.tz'],
            ];

            $app = JobApplication::firstOrCreate(
                ['user_id' => $user->id, 'vacancy_id' => $cData['vacancy']->id],
                [
                    'application_number' => $appNumber,
                    'status' => $cData['status'],
                    'current_step' => $cData['step'],
                    
                    // Step 1
                    'full_name' => $cData['name'],
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'date_of_birth' => '199' . rand(0, 9) . '-01-01',
                    'nida_number' => '19950101' . rand(100000000000, 999999999999),
                    'tin_number' => '102-' . rand(100, 999) . '-' . rand(100, 999),
                    'nssf_number' => 'NSSF-' . rand(10000, 99999),
                    'phone' => $user->phone,
                    'whatsapp_number' => $user->phone,
                    'email' => $cData['email'],
                    'region' => 'Singida',
                    'district' => 'Singida Mjini',
                    'physical_address' => '123 Campus Road',

                    // Step 3
                    'worked_at_sttc' => true,
                    'sttc_experience' => [
                        'campus' => 'Singida Main Campus',
                        'start_year' => '2023',
                        'end_year' => '2025',
                        'department' => 'IT Department',
                        'position_held' => 'ICT Assistant',
                    ],
                    'experience_history' => $experienceHistory,

                    // Step 4
                    'education_history' => $educationHistory,

                    // Step 5
                    'ict_description' => 'Experienced Laravel coder and database optimizer.',
                    'ict_skills' => $ictSkills,

                    // Step 6
                    'professional_qualifications' => ['CPA', 'NBAA Member'],

                    // Step 7
                    'referees' => $referees,

                    // Step 8
                    'motivation_letter' => 'I would like to offer my engineering expertise to elevate SUPA recruitment dashboards.',

                    // Step 9
                    'attachments' => [
                        'cv' => 'job_documents/' . $appNumber . '/cv.pdf',
                        'cover_letter' => 'job_documents/' . $appNumber . '/cover_letter.pdf',
                        'academic_certificates' => 'job_documents/' . $appNumber . '/cert.pdf',
                        'academic_transcripts' => 'job_documents/' . $appNumber . '/transcript.pdf',
                        'birth_certificate' => 'job_documents/' . $appNumber . '/birth.pdf',
                        'nida' => 'job_documents/' . $appNumber . '/nida.pdf',
                        'passport_photo' => 'job_documents/' . $appNumber . '/photo.jpg',
                    ],

                    // Step 10
                    'certified_correct' => true,
                    'digital_signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                    'declaration_date' => now(),

                    'submitted_at' => (isset($cData['submitted']) && !$cData['submitted']) ? null : now(),
                ]
            );

            // Stage history tracking
            JobApplicationStage::firstOrCreate([
                'job_application_id' => $app->id,
                'stage' => 'Applied',
            ], [
                'comments' => 'Job application initialized.',
                'assigned_hr_officer_id' => $hrManager->id,
            ]);

            if ($cData['status'] !== 'Applied' && (!isset($cData['submitted']) || $cData['submitted'] !== false)) {
                JobApplicationStage::firstOrCreate([
                    'job_application_id' => $app->id,
                    'stage' => $cData['status'],
                ], [
                    'comments' => 'Candidate processed to ' . $cData['status'],
                    'assigned_hr_officer_id' => $hrManager->id,
                ]);
            }

            // Seed Interview if required
            if (isset($cData['interview'])) {
                $interview = Interview::firstOrCreate([
                    'job_application_id' => $app->id,
                ], [
                    'type' => 'Physical',
                    'date' => now()->addDays(3)->toDateString(),
                    'time' => '10:00:00',
                    'venue' => 'Main Boardroom, Block B, Singida Campus',
                    'panel_members' => [$hrManager->id],
                    'instructions' => 'Bring copies of academic certificates.',
                ]);

                if (isset($cData['scorecard'])) {
                    InterviewScorecard::firstOrCreate([
                        'interview_id' => $interview->id,
                        'interviewer_id' => $hrManager->id,
                    ], [
                        'communication' => 8,
                        'technical_knowledge' => 9,
                        'problem_solving' => 8,
                        'leadership' => 7,
                        'teamwork' => 8,
                        'confidence' => 9,
                        'professionalism' => 9,
                        'comments' => 'Excellent developer profile.',
                    ]);
                }
            }

            // Seed Written Test if required
            if (isset($cData['test'])) {
                WrittenTest::firstOrCreate([
                    'job_application_id' => $app->id,
                ], [
                    'test_name' => 'Laravel Practical Coding Exam',
                    'assigned_date' => now()->subDays(2),
                    'questions_file_path' => 'written_tests/q_laravel.pdf',
                    'marks' => 82.5,
                    'script_file_path' => 'written_tests/scripts/a_laravel.pdf',
                    'comments' => 'Graded script attached.',
                    'status' => 'Completed',
                ]);
            }

            // Seed Offer Letter if required
            if (isset($cData['offer'])) {
                $offerLetter = OfferLetter::firstOrCreate([
                    'job_application_id' => $app->id,
                ], [
                    'salary' => 'TZS 3,200,000 / Month',
                    'benefits' => "Full hospital health scheme cover.\nAnnual research grants bonus.",
                    'reporting_date' => now()->addDays(30),
                    'employment_terms' => "Standard 3-month probation period.",
                    'pdf_path' => 'offer_letters/offer_' . $app->id . '.html',
                    'status' => $cData['offer'],
                ]);

                if ($cData['offer'] === 'Accepted') {
                    $offerLetter->update([
                        'digital_signature_path' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                    ]);
                }
            }

            // Seed Talent Pool if required
            if (isset($cData['talent_pool'])) {
                TalentPool::firstOrCreate([
                    'user_id' => $user->id,
                ], [
                    'category' => 'Administrative Pool',
                    'comments' => 'Strong resume.',
                ]);
            }
        }
    }
}

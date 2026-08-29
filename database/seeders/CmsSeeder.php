<?php

namespace Database\Seeders;

use App\Models\Download;
use App\Models\Event;
use App\Models\Faq;
use App\Models\News;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $newsItems = [
            [
                'slug' => 'admission-open-2026-2027',
                'title' => 'Official Opening of Admissions for 2026/2027 Academic Year',
                'summary' => 'Applications are now officially open for all Undergraduate Degree, Diploma, and Postgraduate programmes.',
                'content' => "SINGIDA TEACHERS' TRAINING COLLEGE (STTC) & OUT invites applications from qualified candidates for admission into various undergraduate and postgraduate programmes for the 2026/2027 academic year. Applicants can complete their application online through our admission portal with instant verification of NECTA and NAKTE qualifications.",
                'published_at' => now()->subDays(2),
                'is_featured' => true,
            ],
            [
                'slug' => 'foundation-course-registration-notice',
                'title' => 'Foundation Course (Bridging) Intake Guidelines for 2026/2027',
                'summary' => 'Applicants with Diploma GPA 2.0 - 2.9 are invited to enroll in the Foundation Course for direct degree entry.',
                'content' => 'Candidates who completed Ordinary Diploma with a GPA between 2.0 and 2.9 or FTC certificates are eligible for the Foundation Course program. Upon successful completion, candidates qualify for direct entry into Bachelor degree programs in Education, Arts, and Science.',
                'published_at' => now()->subDays(5),
                'is_featured' => true,
            ],
            [
                'slug' => 'necta-result-verification-system-update',
                'title' => 'Upgraded NECTA & NAKTE Result Verification System',
                'summary' => 'Applicants can now instantly verify ACSEE Form IV & Form VI index numbers online during application.',
                'content' => 'We are pleased to announce the integration of automated result verification for Form IV and Form VI national examinations. Applicants no longer need to submit physical certificates for initial verification.',
                'published_at' => now()->subDays(9),
                'is_featured' => false,
            ],
            [
                'slug' => 'fee-structure-and-control-number-issuance',
                'title' => 'Guide to Application Fee Payments and Control Numbers',
                'summary' => 'Step-by-step instructions on generating government control numbers for payment processing.',
                'content' => 'All application fee payments must be processed using government control numbers (GePG) generated directly inside the applicant dashboard. Payments can be completed via Mobile Money (M-Pesa, Tigo Pesa, Airtel Money) or NMB/CRDB Bank branches.',
                'published_at' => now()->subDays(12),
                'is_featured' => false,
            ],
            [
                'slug' => 'orientation-week-and-registration-calendar',
                'title' => 'Orientation Week & Academic Calendar Release',
                'summary' => 'Important dates for newly admitted students and online orientation schedules.',
                'content' => 'The Directorate of Student Services has released the orientation schedule for the upcoming academic session. Admitted students are encouraged to download their admission letters and report to their designated regional centers.',
                'published_at' => now()->subDays(16),
                'is_featured' => false,
            ],
            [
                'slug' => 'postgraduate-research-grant-announcement',
                'title' => 'Call for Postgraduate Research & Innovation Proposals',
                'summary' => 'Funding opportunities available for Master\'s and Ph.D. research projects in Open Education.',
                'content' => 'The Directorate of Research and Postgraduate Studies invites research proposals from registered Master\'s and Doctoral candidates focusing on distance learning technologies, educational pedagogy, and community development.',
                'published_at' => now()->subDays(20),
                'is_featured' => false,
            ],
        ];

        foreach ($newsItems as $item) {
            News::updateOrCreate(['slug' => $item['slug']], $item);
        }

        Event::firstOrCreate(
            ['title' => 'Virtual Campus Open Day 2026'],
            [
                'location' => 'Online Zoom & Main Campus Auditorium',
                'event_date' => now()->addDays(15),
                'description' => 'Interactive virtual event for prospective applicants to learn about our degree programmes and admission requirements.',
                'is_active' => true,
            ]
        );

        Faq::firstOrCreate(
            ['question' => 'How do I calculate my Admission Category?'],
            [
                'answer' => 'Our system automatically calculates your category. Diploma holders with GPA >= 3.0 or Form Six applicants with 5+ points qualify for Direct Entry. Diploma holders with GPA 2.0 - 2.9 are automatically assigned to the Foundation Course.',
                'category' => 'Admissions',
                'order' => 1,
            ]
        );

        Download::firstOrCreate(
            ['title' => 'Undergraduate Prospectus 2026/2027'],
            [
                'category' => 'Prospectus',
                'file_path' => 'downloads/Prospectus_2026_2027.pdf',
                'file_size_bytes' => 2450000,
            ]
        );
    }
}

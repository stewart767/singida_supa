<?php

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConsentPolicySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'super_admin')->first() ?? User::first();
        $adminId = $admin ? $admin->id : null;

        PrivacyPolicy::updateOrCreate(
            ['version' => '2.1'],
            [
                'title' => 'Privacy Policy & Personal Data Protection Notice',
                'content' => "This Privacy Policy governs the manner in which Singida Teachers' Training College collects, uses, maintains and discloses information collected from users (each, a 'Student Applicant') of the SUPA portal.\n\n1. Personal Identification Information\nWe may collect personal identification information from Student Applicants in a variety of ways, including, but not limited to, when applicants visit our site, register on the site, fill out a form, and in connection with other activities, services, features or resources we make available on our Portal. Student Applicants may be asked for, as appropriate, name, email address, mailing address, phone number, academic records, and identification documents (e.g. NIDA number).\n\n2. Compliance with the Personal Data Protection Act, 2022\nAll personal data processed is handled in strict compliance with the Personal Data Protection Act, 2022 of the United Republic of Tanzania. Data is collected solely for verification of academic eligibility, student record creation, and official communication.\n\n3. Data Retention\nPersonal data of applicants will be retained only as long as necessary for the fulfillment of the purposes for which it was collected, or as required by national archive guidelines.",
                'effective_date' => '2026-07-31',
                'status' => 'Published',
                'published_by' => $adminId,
            ]
        );

        TermsCondition::updateOrCreate(
            ['version' => '2.1'],
            [
                'title' => 'Terms and Conditions of Portal Use & Admission Request',
                'content' => "Please read these Terms and Conditions carefully before completing your admission application.\n\n1. Agreement to Terms\nBy accessing and using this Portal, you agree to be bound by these Terms and Conditions. If you do not agree, you must not proceed with your application.\n\n2. Accuracy of Information\nYou warrant that all information, documents, certificates, and declarations submitted through this application are genuine, true, and accurate. Submitting false, forged, or altered information will lead to immediate rejection, cancellation of admission, and potential legal action.\n\n3. Payment of Application Fees\nApplication fees are non-refundable. Payment must be processed through the generated Government Control Number.\n\n4. Limitation of Liability\nThe College is not liable for network failures, delays, or incomplete submissions caused by third-party services.",
                'effective_date' => '2026-07-31',
                'status' => 'Published',
                'published_by' => $adminId,
            ]
        );
    }
}

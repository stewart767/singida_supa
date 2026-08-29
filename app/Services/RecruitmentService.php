<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\JobApplicationStage;
use App\Models\Vacancy;
use App\Models\TalentPool;
use App\Models\User;
use App\Models\Setting;
use App\Mail\RecruitmentNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecruitmentService
{
    public function __construct(
        protected SmsService $smsService
    ) {}

    public function publishVacancy(Vacancy $vacancy): bool
    {
        $old = $vacancy->toArray();
        $updated = $vacancy->update(['status' => 'Published']);
        if ($updated) {
            AuditLogService::log('vacancy_published', "Vacancy {$vacancy->vacancy_number} was published", [
                'old' => $old,
                'new' => $vacancy->fresh()->toArray()
            ]);
        }
        return $updated;
    }

    public function closeVacancy(Vacancy $vacancy): bool
    {
        $old = $vacancy->toArray();
        $updated = $vacancy->update(['status' => 'Closed']);
        if ($updated) {
            AuditLogService::log('vacancy_closed', "Vacancy {$vacancy->vacancy_number} was closed", [
                'old' => $old,
                'new' => $vacancy->fresh()->toArray()
            ]);
        }
        return $updated;
    }

    public function archiveVacancy(Vacancy $vacancy): bool
    {
        $old = $vacancy->toArray();
        $updated = $vacancy->update(['status' => 'Archived']);
        if ($updated) {
            AuditLogService::log('vacancy_archived', "Vacancy {$vacancy->vacancy_number} was archived", [
                'old' => $old,
                'new' => $vacancy->fresh()->toArray()
            ]);
        }
        return $updated;
    }

    public function duplicateVacancy(Vacancy $vacancy): Vacancy
    {
        return DB::transaction(function () use ($vacancy) {
            $newVacancy = $vacancy->replicate();
            $newVacancy->vacancy_number = 'VAC-' . date('Y') . '-' . strtoupper(Str::random(6));
            $newVacancy->status = 'Draft';
            $newVacancy->save();

            AuditLogService::log('vacancy_duplicated', "Vacancy {$vacancy->vacancy_number} was duplicated to {$newVacancy->vacancy_number}");

            return $newVacancy;
        });
    }

    public function deleteVacancy(Vacancy $vacancy): bool
    {
        return DB::transaction(function () use ($vacancy) {
            $number = $vacancy->vacancy_number;
            $title = $vacancy->job_title;

            if ($vacancy->featured_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($vacancy->featured_image);
            }

            $deleted = $vacancy->delete();
            if ($deleted) {
                AuditLogService::log('vacancy_deleted', "Vacancy {$number} ({$title}) was deleted");
            }
            return $deleted;
        });
    }

    public function transitionStage(JobApplication $application, string $newStage, string $comments = '', ?string $attachment = null, ?User $officer = null): bool
    {
        return DB::transaction(function () use ($application, $newStage, $comments, $attachment, $officer) {
            $oldStatus = $application->status;
            $application->update(['status' => $newStage]);

            $officerId = $officer ? $officer->id : Auth::id();

            // Prepare notification content based on stage
            $jobTitle = $application->vacancy->job_title ?? 'Position';
            $subject = "Update on your job application for {$jobTitle}";
            $emailBody = "";
            $smsMessage = "";

            switch ($newStage) {
                case 'Applied':
                    $emailBody = "<p>Thank you for submitting your application for the position of <strong>{$jobTitle}</strong>.</p><p>We have successfully received your details and documents. Our HR team will review your application soon.</p>";
                    $smsMessage = "Dear {$application->full_name}, your application for {$jobTitle} has been received.";
                    break;
                case 'Under Review':
                    $emailBody = "<p>We wanted to let you know that your application for the position of <strong>{$jobTitle}</strong> is now <strong>Under Review</strong> by our recruitment committee.</p>";
                    $smsMessage = "Dear {$application->full_name}, your application for {$jobTitle} is now under review.";
                    break;
                case 'Shortlisted':
                    $emailBody = "<p>Congratulations! You have been <strong>Shortlisted</strong> for the position of <strong>{$jobTitle}</strong>.</p><p>We will contact you shortly to schedule the next evaluation steps.</p>";
                    $smsMessage = "Congratulations {$application->full_name}, you have been shortlisted for {$jobTitle}!";
                    break;
                case 'Interview Scheduled':
                    $emailBody = "<p>An interview has been <strong>Scheduled</strong> for your application for <strong>{$jobTitle}</strong>.</p><p>Please log in to your career portal to view the interview details, date, venue/link, and instructions.</p>";
                    $smsMessage = "Dear {$application->full_name}, an interview has been scheduled for {$jobTitle}. Check your portal.";
                    break;
                case 'Written Test':
                    $emailBody = "<p>A <strong>Written Test</strong> has been scheduled for your application for <strong>{$jobTitle}</strong>.</p><p>Please check your career portal for test instructions and script submission details.</p>";
                    $smsMessage = "Dear {$application->full_name}, a written test is scheduled for {$jobTitle}. Check your portal.";
                    break;
                case 'Interview Result':
                case 'Final Interview':
                    $emailBody = "<p>You have been progressed to the <strong>Final Interview</strong> stage for <strong>{$jobTitle}</strong>.</p><p>Please check your portal for instructions.</p>";
                    $smsMessage = "Dear {$application->full_name}, you have progressed to final interviews for {$jobTitle}.";
                    break;
                case 'Selected':
                    $emailBody = "<p>Congratulations! You have been <strong>Selected</strong> for the position of <strong>{$jobTitle}</strong>.</p><p>We are preparing your official offer letter and will issue it shortly.</p>";
                    $smsMessage = "Congratulations {$application->full_name}, you have been selected for {$jobTitle}!";
                    break;
                case 'Offer Letter':
                    $emailBody = "<p>Your official <strong>Employment Offer Letter</strong> has been issued for <strong>{$jobTitle}</strong>.</p><p>Please log in to your career portal to view the letter and sign digitally to accept the offer.</p>";
                    $smsMessage = "Dear {$application->full_name}, your offer letter for {$jobTitle} is issued. Please log in to sign.";
                    break;
                case 'Hired':
                    $emailBody = "<p>Welcome to SUPA / OUT University! You have been officially <strong>Hired</strong> for the position of <strong>{$jobTitle}</strong>.</p><p>Our HR department will contact you with onboarding instructions shortly.</p>";
                    $smsMessage = "Welcome to the team {$application->full_name}! You are officially hired for {$jobTitle}.";
                    break;
                case 'Rejected':
                    $emailBody = "<p>Thank you for your interest in the <strong>{$jobTitle}</strong> position. Unfortunately, after careful consideration, we have decided not to move forward with your application at this time.</p><p>We wish you all the best in your job search.</p>";
                    $smsMessage = "Dear {$application->full_name}, your application for {$jobTitle} was not successful.";
                    break;
                default:
                    $emailBody = "<p>Your application status has been updated to: <strong>{$newStage}</strong>.</p>";
                    $smsMessage = "Dear {$application->full_name}, your application status is now {$newStage}.";
                    break;
            }

            // Dispatch email notification
            $emailSent = false;
            if (Setting::get('enable_recruitment_email_notifications', true)) {
                try {
                    Mail::to($application->email)->send(new RecruitmentNotificationMail($application->full_name, $subject, $emailBody));
                    $emailSent = true;
                } catch (\Throwable $e) {}
            }

            // Dispatch SMS notification
            $smsSent = false;
            if (Setting::get('enable_recruitment_sms_notifications', true)) {
                $smsSent = $this->smsService->send($application->phone, $smsMessage);
            }

            // Save stage history log
            JobApplicationStage::create([
                'job_application_id' => $application->id,
                'stage' => $newStage,
                'assigned_hr_officer_id' => $officerId,
                'comments' => $comments,
                'attachments' => $attachment ? [$attachment] : null,
                'notification_history' => [
                    'email' => $emailSent,
                    'sms' => $smsSent,
                    'email_recipient' => $application->email,
                    'sms_recipient' => $application->phone,
                    'timestamp' => now()->toDateTimeString()
                ]
            ]);

            // Save system AuditLog
            AuditLogService::log('applicant_stage_transitioned', "Application {$application->application_number} transitioned from {$oldStatus} to {$newStage}", [
                'old' => ['status' => $oldStatus],
                'new' => ['status' => $newStage, 'comments' => $comments]
            ]);

            return true;
        });
    }

    public function addToTalentPool(User $user, string $category, string $comments = ''): TalentPool
    {
        return DB::transaction(function () use ($user, $category, $comments) {
            $pool = TalentPool::create([
                'user_id' => $user->id,
                'category' => $category,
                'comments' => $comments,
            ]);

            AuditLogService::log('talent_pool_added', "User {$user->name} added to {$category} talent pool", [
                'user_id' => $user->id,
                'category' => $category
            ]);

            return $pool;
        });
    }

    public function getFunnelReport(): array
    {
        $stages = [
            'Applied', 'Screening', 'Under Review', 'Shortlisted', 'Interview', 
            'Written Test', 'Final Interview', 'Selected', 'Offer Letter', 'Hired', 'Rejected'
        ];
        $counts = [];
        foreach ($stages as $stage) {
            $counts[$stage] = JobApplication::where('status', $stage)->count();
        }
        return $counts;
    }

    public function getGenderReport(): array
    {
        return [
            'Male' => JobApplication::where('gender', 'male')->count(),
            'Female' => JobApplication::where('gender', 'female')->count(),
            'Other' => JobApplication::where('gender', 'other')->count() + JobApplication::whereNull('gender')->count(),
        ];
    }

    public function getEducationReport(): array
    {
        $levels = ['Secondary', 'Certificate', 'Diploma', 'Bachelor', 'Postgraduate Diploma', 'Master\'s', 'PhD', 'Other'];
        $counts = array_fill_keys($levels, 0);

        // We can inspect the education_history JSON arrays
        $apps = JobApplication::whereNotNull('education_history')->get();
        foreach ($apps as $app) {
            $history = $app->education_history;
            if (is_array($history)) {
                foreach ($history as $edu) {
                    $lvl = $edu['level'] ?? 'Other';
                    if (array_key_exists($lvl, $counts)) {
                        $counts[$lvl]++;
                    } else {
                        $counts['Other']++;
                    }
                }
            }
        }
        return $counts;
    }

    public function getCampusReport(): array
    {
        $campuses = \App\Models\Campus::all();
        $counts = [];
        foreach ($campuses as $campus) {
            $counts[$campus->name] = JobApplication::whereHas('vacancy', function($q) use ($campus) {
                $q->where('campus_id', $campus->id);
            })->count();
        }
        return $counts;
    }

    public function getCSVExport(array $headers, array $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        return $csv;
    }
}

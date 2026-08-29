<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JobApplication;
use App\Models\Vacancy;

class RecruitmentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    public function viewVacancies(User $user): bool
    {
        return $user->hasPermissionTo('manage_vacancies') || $user->hasRole(['hr_director', 'hr_manager', 'hr_officer', 'designation_head']);
    }

    public function manageCategories(User $user): bool
    {
        return $user->hasPermissionTo('manage_job_categories');
    }

    public function manageDesignations(User $user): bool
    {
        return $user->hasPermissionTo('manage_designations');
    }

    public function managePositions(User $user): bool
    {
        return $user->hasPermissionTo('manage_positions');
    }

    public function manageVacancies(User $user): bool
    {
        return $user->hasPermissionTo('manage_vacancies');
    }

    public function viewApplications(User $user): bool
    {
        return $user->hasPermissionTo('manage_job_applications') || $user->hasRole(['hr_director', 'hr_manager', 'hr_officer', 'designation_head']);
    }

    public function viewApplication(User $user, JobApplication $application): bool
    {
        if ($application->user_id === $user->id) {
            return true;
        }
        return $user->hasPermissionTo('manage_job_applications') || $user->hasRole(['hr_director', 'hr_manager', 'hr_officer', 'designation_head']);
    }

    public function shortlist(User $user): bool
    {
        return $user->hasPermissionTo('shortlist_applicants') || $user->hasRole(['hr_director', 'hr_manager', 'hr_officer']);
    }

    public function scheduleInterviews(User $user): bool
    {
        return $user->hasPermissionTo('schedule_interviews') || $user->hasRole(['hr_director', 'hr_manager', 'hr_officer']);
    }

    public function scoreInterviews(User $user): bool
    {
        return $user->hasPermissionTo('score_interviews') || $user->hasRole(['hr_director', 'hr_manager', 'hr_officer', 'interview_panel']);
    }

    public function evaluate(User $user): bool
    {
        return $user->hasPermissionTo('evaluate_applicants') || $user->hasRole(['hr_director', 'hr_manager']);
    }

    public function generateOfferLetters(User $user): bool
    {
        return $user->hasPermissionTo('generate_offer_letters') || $user->hasRole(['hr_director', 'hr_manager']);
    }

    public function manageTalentPool(User $user): bool
    {
        return $user->hasPermissionTo('manage_talent_pool') || $user->hasRole(['hr_director', 'hr_manager', 'hr_officer']);
    }

    public function viewReports(User $user): bool
    {
        return $user->hasPermissionTo('view_recruitment_reports') || $user->hasRole(['hr_director', 'hr_manager']);
    }

    public function manageSettings(User $user): bool
    {
        return $user->hasPermissionTo('manage_recruitment_settings') || $user->hasRole('super_admin');
    }
}

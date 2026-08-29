<?php

namespace App\Repositories\Eloquent;

use App\Models\JobApplicationIntent;
use App\Repositories\Contracts\JobApplicationIntentRepositoryInterface;

class JobApplicationIntentRepository implements JobApplicationIntentRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function find(int $id): ?JobApplicationIntent
    {
        return JobApplicationIntent::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): JobApplicationIntent
    {
        return JobApplicationIntent::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): bool
    {
        $intent = JobApplicationIntent::find($id);
        return $intent ? $intent->update($data) : false;
    }

    /**
     * {@inheritdoc}
     */
    public function findForUserVacancy(int $userId, int $vacancyId): ?JobApplicationIntent
    {
        return JobApplicationIntent::where('user_id', $userId)
            ->where('vacancy_id', $vacancyId)
            ->first();
    }
}

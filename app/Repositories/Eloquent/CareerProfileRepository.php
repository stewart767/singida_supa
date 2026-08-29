<?php

namespace App\Repositories\Eloquent;

use App\Models\CareerProfile;
use App\Repositories\Contracts\CareerProfileRepositoryInterface;

class CareerProfileRepository implements CareerProfileRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForUser(int $userId): ?CareerProfile
    {
        return CareerProfile::where('user_id', $userId)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): CareerProfile
    {
        return CareerProfile::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): bool
    {
        $profile = CareerProfile::find($id);
        return $profile ? $profile->update($data) : false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $profile = CareerProfile::find($id);
        return $profile ? $profile->delete() : false;
    }
}

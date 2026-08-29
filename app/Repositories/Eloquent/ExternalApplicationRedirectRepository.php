<?php

namespace App\Repositories\Eloquent;

use App\Models\ExternalApplicationRedirect;
use App\Repositories\Contracts\ExternalApplicationRedirectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ExternalApplicationRedirectRepository implements ExternalApplicationRedirectRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function find(int $id): ?ExternalApplicationRedirect
    {
        return ExternalApplicationRedirect::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByToken(string $token): ?ExternalApplicationRedirect
    {
        return ExternalApplicationRedirect::where('tracking_token', $token)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): ExternalApplicationRedirect
    {
        return ExternalApplicationRedirect::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function allForUser(int $userId): Collection
    {
        return ExternalApplicationRedirect::where('user_id', $userId)->latest()->get();
    }
}

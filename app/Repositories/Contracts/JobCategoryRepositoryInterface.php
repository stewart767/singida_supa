<?php

namespace App\Repositories\Contracts;

use App\Models\JobCategory;
use Illuminate\Database\Eloquent\Collection;

interface JobCategoryRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?JobCategory;
    public function create(array $data): JobCategory;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

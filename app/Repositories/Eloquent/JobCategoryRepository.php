<?php

namespace App\Repositories\Eloquent;

use App\Models\JobCategory;
use App\Repositories\Contracts\JobCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class JobCategoryRepository implements JobCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return JobCategory::orderBy('display_order')->orderBy('name')->get();
    }

    public function find(int $id): ?JobCategory
    {
        return JobCategory::find($id);
    }

    public function create(array $data): JobCategory
    {
        return JobCategory::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = $this->find($id);
        return $category ? $category->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $category = $this->find($id);
        return $category ? $category->delete() : false;
    }
}

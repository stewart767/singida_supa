<?php

namespace App\Repositories\Eloquent;

use App\Models\Campus;
use App\Repositories\Contracts\CampusRepositoryInterface;
use Illuminate\Support\Collection;

class CampusRepository implements CampusRepositoryInterface
{
    public function all(): Collection
    {
        return Campus::orderBy('name', 'asc')->get();
    }

    public function find(int $id): ?Campus
    {
        return Campus::find($id);
    }

    public function create(array $data): Campus
    {
        return Campus::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $campus = Campus::find($id);
        if (!$campus) {
            return false;
        }
        return $campus->update($data);
    }

    public function delete(int $id): bool
    {
        $campus = Campus::find($id);
        if (!$campus) {
            return false;
        }
        return $campus->delete();
    }
}

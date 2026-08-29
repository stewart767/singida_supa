<?php

namespace App\Repositories\Eloquent;

use App\Models\Designation;
use App\Repositories\Contracts\DesignationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DesignationRepository implements DesignationRepositoryInterface
{
    public function all(): Collection
    {
        return Designation::with('headOfDesignation')->orderBy('name')->get();
    }

    public function find(int $id): ?Designation
    {
        return Designation::with('headOfDesignation')->find($id);
    }

    public function create(array $data): Designation
    {
        return Designation::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $designation = $this->find($id);
        return $designation ? $designation->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $designation = $this->find($id);
        return $designation ? $designation->delete() : false;
    }
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Collection;

interface VacancyRepositoryInterface
{
    public function all(): Collection;
    public function allPublished(): Collection;
    public function find(int $id): ?Vacancy;
    public function findByVacancyNumber(string $number): ?Vacancy;
    public function create(array $data): Vacancy;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

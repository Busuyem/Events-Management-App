<?php

namespace App\Interfaces;

use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EventRepositoryInterface
{
    public function all(array $filters = [], int $perPage = 10): LengthAwarePaginator;
    public function find(int $id): ?Event;
    public function create(array $data): Event;
    public function update(int $id, array $data): Event;
    public function delete(int $id): bool;
}

<?php

namespace App\Interfaces;

use App\Models\Ticket;

interface TicketRepositoryInterface
{
    public function create(array $data): Ticket;
    public function find(int $id): ?Ticket;
    public function update(int $id, array $data): Ticket;
    public function delete(int $id): bool;
}

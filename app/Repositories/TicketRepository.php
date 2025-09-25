<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Interfaces\TicketRepositoryInterface;

class TicketRepository implements TicketRepositoryInterface
{
    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function find(int $id): ?Ticket
    {
        return Ticket::with('event')->find($id);
    }

   public function update(int $id, array $data): Ticket
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->fill($data);
        $ticket->save();

        return $ticket->fresh();
    }

    public function delete(int $id): bool
    {
        $ticket = Ticket::findOrFail($id);
        return (bool) $ticket->delete();
    }
}

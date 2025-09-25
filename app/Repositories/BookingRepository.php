<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Interfaces\BookingRepositoryInterface;
use Illuminate\Support\Collection;

class BookingRepository implements BookingRepositoryInterface
{
    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function find(int $id): ?Booking
    {
        return Booking::with(['ticket', 'user', 'payment'])->find($id);
    }

    public function userBookings(int $userId): Collection
    {
        return Booking::with(['ticket.event', 'payment'])
            ->where('user_id', $userId)
            ->get();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $booking = Booking::findOrFail($id);
        $booking->status = $status;
        return $booking->save();
    }
}

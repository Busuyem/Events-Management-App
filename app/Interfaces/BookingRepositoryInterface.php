<?php

namespace App\Interfaces;

use App\Models\Booking;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface
{
    public function create(array $data): Booking;
    public function find(int $id): ?Booking;
    public function userBookings(int $userId): Collection;
    public function updateStatus(int $id, string $status): bool;
}

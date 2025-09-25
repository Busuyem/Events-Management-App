<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function find(int $id): ?Payment
    {
        return Payment::with('booking')->find($id);
    }
}

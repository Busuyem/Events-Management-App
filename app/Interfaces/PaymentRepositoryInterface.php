<?php

namespace App\Interfaces;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function create(array $data): Payment;
    public function find(int $id): ?Payment;
}

<?php

namespace App\Services;

use App\Models\Booking;

class PaymentService
{
    /**
     * Simulate a payment process.
     * Randomly return success or failure.
     */
    public function process(Booking $booking, float $amount): array
    {
        // 70% chance of success
        $isSuccess = rand(1, 10) <= 7;

        return [
            'status' => $isSuccess ? 'success' : 'failed',
            'amount' => $amount,
        ];
    }
}

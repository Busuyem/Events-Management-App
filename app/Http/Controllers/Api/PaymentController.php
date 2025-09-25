<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Repositories\BookingRepository;
use App\Repositories\PaymentRepository;
use App\Services\PaymentService;
use App\Notifications\BookingConfirmedNotification;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    protected BookingRepository $bookings;
    protected PaymentRepository $payments;
    protected PaymentService $paymentService;

    public function __construct(
        BookingRepository $bookings,
        PaymentRepository $payments,
        PaymentService $paymentService
    ) {
        $this->bookings = $bookings;
        $this->payments = $payments;
        $this->paymentService = $paymentService;
    }

    public function store($bookingId)
    {
        try {
            $booking = $this->bookings->find($bookingId);

            if (! $booking || $booking->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking already processed'
                ], Response::HTTP_BAD_REQUEST);
            }

            $amount = $booking->ticket->price * $booking->quantity;

            $result = $this->paymentService->process($booking, $amount);

            $payment = $this->payments->create([
                'booking_id' => $booking->id,
                'amount'     => $result['amount'],
                'status'     => $result['status'],
            ]);

            // Update booking + trigger notification if successful
            if ($result['status'] === 'success') {
                $this->bookings->updateStatus($booking->id, 'confirmed');

                // Send confirmation notification (queued)
                $booking->user->notify(new BookingConfirmedNotification($booking));
            } else {
                $this->bookings->updateStatus($booking->id, 'cancelled');

                // Refund ticket quantity
                $booking->ticket->increment('quantity', $booking->quantity);
            }

            return (new PaymentResource($payment))
                ->additional([
                    'success' => true,
                    'message' => 'Payment processed',
                ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            Log::error('Payment store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $payment = $this->payments->find($id);

            if (! $payment || $payment->booking->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return (new PaymentResource($payment))
                ->additional([
                    'success' => true,
                    'message' => 'Payment details retrieved',
                ]);
        } catch (\Throwable $e) {
            Log::error('Payment show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payment'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

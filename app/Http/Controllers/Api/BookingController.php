<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BookingRepository;
use App\Repositories\TicketRepository;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    protected BookingRepository $bookings;
    protected TicketRepository $tickets;

    public function __construct(BookingRepository $bookings, TicketRepository $tickets)
    {
        $this->bookings = $bookings;
        $this->tickets = $tickets;
    }

    public function store(StoreBookingRequest $request, $ticketId)
    {
        try {
            $ticket = $this->tickets->find($ticketId);

            if (! $ticket) {
                return response()->json(['success' => false, 'message' => 'Ticket not found'], Response::HTTP_NOT_FOUND);
            }

            if ($ticket->quantity < $request->quantity) {
                return response()->json(['success' => false, 'message' => 'Not enough tickets available'], Response::HTTP_BAD_REQUEST);
            }

            // Reduce ticket quantity
            $ticket->decrement('quantity', $request->quantity);

            $booking = $this->bookings->create([
                'user_id'   => $request->user()->id,
                'ticket_id' => $ticket->id,
                'quantity'  => $request->quantity,
                'status'    => 'pending',
            ]);

            return (new BookingResource($booking))
                ->additional([
                    'success' => true,
                    'message' => 'Booking created successfully',
                ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            Log::error('Booking store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating booking'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index()
    {
        try {
            $bookings = $this->bookings->userBookings(auth()->id());

            return BookingResource::collection($bookings)
                ->additional([
                    'success' => true,
                    'message' => 'Bookings fetched successfully',
                ]);
        } catch (\Throwable $e) {
            Log::error('Booking index error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching bookings'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancel($id)
    {
        try {
            $booking = $this->bookings->find($id);

            if (! $booking || $booking->user_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Booking not found'], Response::HTTP_NOT_FOUND);
            }

            if ($booking->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Cannot cancel this booking'], Response::HTTP_BAD_REQUEST);
            }

            $this->bookings->updateStatus($id, 'cancelled');

            // Restore ticket quantity
            $booking->ticket->increment('quantity', $booking->quantity);

            return (new BookingResource($booking->refresh()))
                ->additional([
                    'success' => true,
                    'message' => 'Booking cancelled successfully',
                ]);
        } catch (\Throwable $e) {
            Log::error('Booking cancel error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error cancelling booking'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

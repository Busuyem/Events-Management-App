<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDoubleBooking
{
    // public function handle(Request $request, Closure $next): Response
    // {
    //     $userId = $request->user()->id;

    //     // Supports both /tickets/{id}/bookings and /tickets/{ticket}/bookings
    //     $ticketId = (int) $request->route('id') ?? (int) $request->route('ticket');

    //     $existing = Booking::where('user_id', $userId)
    //         ->where('ticket_id', $ticketId)
    //         ->whereIn('status', ['pending', 'confirmed'])
    //         ->exists();

    //     if ($existing) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'You already have a booking for this ticket.',
    //         ], Response::HTTP_CONFLICT); // 409 instead of 400
    //     }

    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $ticketId = (int) $request->route('id') ?? (int) $request->route('ticket');

        $existing = Booking::where('user_id', $user->id)
            ->where('ticket_id', $ticketId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a booking for this ticket.',
            ], Response::HTTP_CONFLICT);
        }

        return $next($request);
    }

}

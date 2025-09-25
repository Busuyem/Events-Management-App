<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Repositories\TicketRepository;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
    protected TicketRepository $tickets;

    public function __construct(TicketRepository $tickets)
    {
        $this->tickets = $tickets;
    }

    public function store(StoreTicketRequest $request, $eventId)
    {
        try {
            $payload = $request->validated();

            $event = Event::find($eventId);

            if (! $event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $payload['event_id'] = $event->id;

            $ticket = $this->tickets->create($payload);

            return (new TicketResource($ticket))
                ->additional([
                    'success' => true,
                    'message' => 'Ticket created successfully',
                ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            Log::error('Ticket store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating ticket'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function update(UpdateTicketRequest $request, $id)
    {
        try {
            $payload = $request->validated();
            $ticket = $this->tickets->update($id, $payload);

            return (new TicketResource($ticket))
                ->additional([
                    'success' => true,
                    'message' => 'Ticket updated successfully',
                ]);
        } catch (\Throwable $e) {
            Log::error('Ticket update error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function destroy($id)
    {
        try {
            $deleted = $this->tickets->delete($id);

            if (! $deleted) {
                return response()->json(['success' => false, 'message' => 'Ticket not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['success' => true, 'message' => 'Ticket deleted successfully'], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Ticket delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting ticket'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

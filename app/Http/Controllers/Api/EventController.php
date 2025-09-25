<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\EventRepository;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    protected EventRepository $events;

    public function __construct(EventRepository $events)
    {
        $this->events = $events;
    }

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['title', 'date', 'location']);
            $perPage = $request->get('per_page', 10);

            $events = $this->events->all($filters, $perPage);

            return EventResource::collection($events)
                ->additional([
                    'success' => true,
                    'message' => 'Events fetched successfully',
                ]);
        } catch (\Throwable $e) {
            Log::error('Event index error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching events'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $event = $this->events->find($id);

            if (! $event) {
                return response()->json(['success' => false, 'message' => 'Event not found'], Response::HTTP_NOT_FOUND);
            }

            return (new EventResource($event))->additional(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Event show error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching event'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreEventRequest $request)
    {
        try {
            $payload = $request->validated();
            $payload['created_by'] = $request->user()->id;

            $event = $this->events->create($payload);

            return (new EventResource($event))
                ->additional([
                    'success' => true,
                    'message' => 'Event created successfully',
                ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            Log::error('Event store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating event'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

   
    public function update(UpdateEventRequest $request, $id)
{
    try {
        $payload = $request->validated();
        $event = $this->events->update($id, $payload);

        return (new EventResource($event))
            ->additional([
                'success' => true,
                'message' => 'Event updated successfully',
            ]);
    } catch (\Throwable $e) {
        Log::error('Event update error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error updating event'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}



    public function destroy($id)
    {
        try {
            $deleted = $this->events->delete($id);

            if (! $deleted) {
                return response()->json(['success' => false, 'message' => 'Event not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['success' => true, 'message' => 'Event deleted successfully'], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Event delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting event'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

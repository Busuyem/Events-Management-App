<?php

namespace App\Repositories;

use App\Models\Event;
use App\Interfaces\EventRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class EventRepository implements EventRepositoryInterface
{
    public function all(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $page = request()->get('page', 1);

        // Generate a unique cache key based on filters + page
        $cacheKey = 'events_' . md5(json_encode($filters) . "_page_$page");

        return Cache::remember($cacheKey, 60, function () use ($filters, $perPage) {
            $query = Event::with('tickets')
                ->searchByTitle($filters['title'] ?? null)
                ->filterByDate($filters['date'] ?? null);

            if (!empty($filters['location'])) {
                $query->where('location', 'LIKE', "%{$filters['location']}%");
            }

            return $query->paginate($perPage);
        });
    }

    public function find(int $id): ?Event
    {
        return Event::with('tickets')->find($id);
    }

    public function create(array $data): Event
    {
        Cache::flush();
        return Event::create($data);
    }

   public function update(int $id, array $data): Event
    {
        $event = Event::findOrFail($id);
        $event->update($data);

        // Clear only relevant cache keys
        Cache::forget("event_{$id}");
        Cache::forget("events_all");

        return $event->fresh(); // return updated model
    }


    public function delete(int $id): bool
    {
        $event = Event::findOrFail($id);
        Cache::flush();
        return (bool) $event->delete();
    }
}

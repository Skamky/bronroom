<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Raspisanie;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventService
{
    public function checkEventOverlap(Room $room, string $startDate, string $endDate, ?int $excludeEventId = null): bool
    {
        $query = Raspisanie::where('room_id', $room->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                    });
            });

        if ($excludeEventId !== null) {
            $query->where('event_id', '!=', $excludeEventId);
        }

        return $query->exists();
    }

    public function createEvent(array $data, int $userId): Event
    {
        DB::beginTransaction();
        try {
            $event = new Event();
            $event->name = $data['title'];
            $event->description = $data['description'];
            $event->user_id = $userId;
            $event->save();

            $rasp = new Raspisanie();
            $rasp->start_date = $data['start_date'];
            $rasp->end_date = $data['end_date'];
            $rasp->room_id = $data['room_id'];
            $rasp->event_id = $event->id;
            $rasp->save();

            DB::commit();
            return $event;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating event: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateEvent(Event $event, array $data): Event
    {
        DB::beginTransaction();
        try {
            $event->name = $data['title'];
            $event->description = $data['description'];
            $event->save();

            $rasp = $event->raspisanie->first();
            if (!$rasp) {
                throw new \RuntimeException('Event schedule not found');
            }

            $rasp->start_date = $data['start_date'];
            $rasp->end_date = $data['end_date'];
            $rasp->room_id = $data['room_id'];
            $rasp->save();

            DB::commit();
            return $event;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating event: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteEvent(Event $event): void
    {
        $event->deleteOrFail();
    }
} 
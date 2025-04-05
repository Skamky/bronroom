<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Office;
use App\Models\Room;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BreadcrumbsService
{
    //todo дополнить для других роутов
    public static function generate(): array
    {
        $segments = collect(explode('/', trim(request()->path(), '/')));

        $breadcrumbs = collect([['url' => '/offices', 'text' => 'Главная']]);

        try {
            if ($segments->get(0) === 'offices' && $segments->has(1)) {
                $office = Office::findOrFail($segments->get(1));
                $breadcrumbs->push(['url' => "/offices/{$office->id}", 'text' => $office->name]);

                if ($segments->get(2) === 'rooms' && $segments->has(3)) {
                    $room = Room::where('office_id', $office->id)->findOrFail($segments->get(3));
                    $breadcrumbs->push(['url' => "/offices/{$office->id}/rooms/{$room->id}", 'text' => $room->name]);

                    if ($segments->get(4) === 'events' && $segments->has(5)) {
                        $event = Event::findOrFail($segments->get(5));
                        $breadcrumbs->push([
                            'url' => "/offices/{$office->id}/rooms/{$room->id}/events/{$event->id}",
                            'text' => $event->name // или другое поле, которое вы хотите отображать
                        ]);
                    }
                }
            }
        } catch (ModelNotFoundException $e) {
            logger()->warning("Breadcrumbs entity not found: " . $e->getMessage());
        }

        return $breadcrumbs->toArray();
    }
}

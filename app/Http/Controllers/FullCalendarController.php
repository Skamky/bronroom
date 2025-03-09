<?php

namespace App\Http\Controllers;

use App\Http\Requests\FullCalendarRequest;
use App\Models\Room;
use Illuminate\Database\Eloquent\Relations\Relation;

class FullCalendarController extends Controller
{
    public function index(FullCalendarRequest $request): \Illuminate\Http\JsonResponse
    {
        /** @var array $valid */
        $valid = $request->validated();
        $rooms = Room::query()->whereIn('id', $valid['rooms'])
            ->with(
                ['events' =>
                    fn(Relation $query) => $query->where(function ($query) use ($valid) {
                        $query->where(function ($query) use ($valid) {
                            $query->where('raspisanies.start_date', '>=', $valid['start'])
                                ->where('raspisanies.start_date', '<=', $valid['end']);
                        })
                            ->orWhere(function ($query) use ($valid) {
                                $query->where('raspisanies.end_date', '>=', $valid['start'])
                                    ->where('raspisanies.end_date', '<=', $valid['end']);
                            })
                            ->orWhere(function ($query) use ($valid) {
                                $query->where('raspisanies.start_date', '<=', $valid['start'])
                                    ->where('raspisanies.end_date', '>=', $valid['end']);
                            });
                    })
                ]
            )->get();


        $result = [];

        foreach ($rooms as $room) {
            foreach ($room->events as $event) {
                $result[] = [
                    'title' => $event->name,
                    'start' => $event->pivot->start_date->toIso8601String(),
                    'end' => $event->pivot->end_date->toIso8601String(),
                ];
            }
        }
        //todo вынести  логику парсинга в ресурс
        //TODO добавить цвет  если выводиться событие конкретного пользователя

        return response()->json($result);
    }
}

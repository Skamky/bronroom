<?php

namespace App\Http\Controllers;

use App\Http\Requests\FullCalendarRequest;
use App\Models\Event;
use App\Models\Room;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

//TODO добавить отдельный обработчик только своих событий
class FullCalendarController extends Controller
{
    public function index(FullCalendarRequest $request): \Illuminate\Http\JsonResponse
    {
        $current_user_id = Auth::id();
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
            /** @var Event $event */
            foreach ($room->events as $event) {
                $result[] = [
                    'id' => $event->id,
                    'title' => $event->name,
                    'start' => $event->pivot->start_date->toIso8601String(),
                    'end' => $event->pivot->end_date->toIso8601String(),
                    ...($current_user_id == $event->user_id ? [] : ['backgroundColor' => 'orange']),
                    'user_id' => $event->user_id,
                ];
            }
        }
        //todo вынести  логику парсинга в ресурс
        //TODO добавить цвет  если выводиться событие конкретного пользователя

        return response()->json($result);
    }
}

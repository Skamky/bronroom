<?php

namespace App\Http\Controllers;

use App\Http\Requests\FullCalendarRequest;
use App\Models\Room;

class FullCalendarController extends Controller
{
    public function index(FullCalendarRequest $request)
    {
        /** @var array $valid */
        $valid = $request->validated();

        //TODO продумать жадную загрузку связей
        $rooms = Room::query()->where('id', $valid['rooms'])->with('events')->get();


        $result = [];

        foreach ($rooms as $room) {
            foreach ($room->events as $event) {


                $result[] = [
                    'title' => $event->name,
                    'start' => $event->pivot->start_date,
                    'end' => $event->pivot->end_date,
                ];
            }
        }
//todo вынести  логику парсинга в ресурс
        //TODO добавить цвет  если выводиться событие конкретного пользователя

        return response()->json($result);


    }
}

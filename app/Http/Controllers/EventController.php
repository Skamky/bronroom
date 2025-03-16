<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Raspisanie;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    //GET|HEAD        events  events.index › EventController@index
    //POST            events  events.store › EventController@store
    //GET|HEAD        events/{event} ...... events.show › EventController@show
    //  PUT|PATCH       events/{event} .. events.update › EventController@update
    //  DELETE          events/{event}  events.destroy › EventController@destroy




    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    //    /**
    //     * Show the form for creating a new resource.
    //     */
    //    public function create()
    //    {
    //        //
    //    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $room = Room::query()->findOrFail($data['room_id']);

            $event = new Event();
            $event->name = $data['title'];
            $event->description = $data['description'];
            $event->user_id = Auth::id();
            $event->save();

            // Создаем расписание
            $rasp = new Raspisanie();
            $rasp->start_date = $data['start_date'];
            $rasp->end_date = $data['end_date'];
            $rasp->room_id = $room->id;
            $rasp->event_id = $event->id;
            $rasp->save();

            DB::commit();

            //TODO сделть редирект на страницу события
            return response()->json([
                'event' => $event,
                'rasp' => $rasp,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating event: ' . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    //    /**
    //     * Show the form for editing the specified resource.
    //     */
    //    public function edit(Event $event)
    //    {
    //        //
    //    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }
}

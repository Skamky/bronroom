<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Office;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Office $office $
     * @return JsonResponse
     */
    public function index(Office $office)
    {
        $rooms = $office->rooms;
        return response()->json($rooms);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Office $office, Room $room)
    {
//        dd($office_id, $room);
//        $room = Room::query()->findOrFail($room);
        return Inertia::render('Room', ['room' => $room]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Enum\ToastTheme;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Office;
use App\Models\Raspisanie;
use App\Models\Room;
use App\Notifications\Toast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

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
     *  TODO покрыть тестами
     */
    public function store(StoreEventRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $room = Room::query()->findOrFail($data['room_id']);

        // Проверка на пересечение событий
        $overlap = Raspisanie::where('room_id', $data['room_id'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                    ->orWhere(function ($query) use ($data) {
                        $query->where('start_date', '<', $data['start_date'])
                            ->where('end_date', '>', $data['end_date']);
                    });
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->withErrors([
                'overlap' => 'Данное место и время проведения уже заняты!',
            ]);
        }

        DB::beginTransaction();
        try {
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

            // Перенаправляем на страницу редактирования
            return redirect()->route('event.edit', [$room->office_id, $room->id, $event->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating event: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Office $office, Room $room, Event $event): \Inertia\Response
    {
        if ($office->id != $room->office_id) {
            abort(404, 'данная команата не принадлежит данному оффису');
        }
        if (!$event->rooms->contains($room)) {
            abort(404, 'Событие не принадлежит указанной комнате');
        }

        $event->user;
        //todo переделать как появяться отличные от single события
        $r = $event->raspisanie[0];

        $event->start = $r->start_date;
        $event->end = $r->end_date;

        return Inertia::render('ViewEventPage', ['event' => $event, 'room' => $room]);
    }

    public function getEventData(Event $event)
    {
        $event->load(['user', 'raspisanie']);
        $r = $event->raspisanie[0];
        
        return response()->json([
            'id' => $event->id,
            'title' => $event->name,
            'description' => $event->description,
            'start' => $r->start_date,
            'end' => $r->end_date,
            'user' => [
                'id' => $event->user->id,
                'name' => $event->user->name,
            ],
            'created_at' => $event->created_at,
            'updated_at' => $event->updated_at,
        ]);
    }

    public function redirect_show(Event $event): \Illuminate\Http\RedirectResponse
    {
        $room = $event->rooms[0];
        return redirect()->route('event.show', [$room->office_id, $room->id, $event->id]);
    }

    public function redirect_edit(Event $event): \Illuminate\Http\RedirectResponse
    {
        $room = $event->rooms[0];
        return redirect()->route('event.edit', [$room->office_id, $room->id, $event->id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Office $office, Room $room, Event $event): \Inertia\Response
    {
        if ($office->id != $room->office_id) {
            abort(404, 'данная комната не принадлежит данному офису');
        }
        if (!$event->rooms->contains($room)) {
            abort(404, 'Событие не принадлежит указанной комнате');
        }

        $event->load('user');
        $r = $event->raspisanie[0];
        $event->start = $r->start_date;
        $event->end = $r->end_date;

        return Inertia::render('EditEventPage', ['event' => $event, 'room' => $room]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $room = Room::query()->findOrFail($data['room_id']);

        // Проверка на пересечение событий (исключая текущее событие)
        $overlap = Raspisanie::where('room_id', $data['room_id'])
            ->where('event_id', '!=', $event->id)
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                    ->orWhere(function ($query) use ($data) {
                        $query->where('start_date', '<', $data['start_date'])
                            ->where('end_date', '>', $data['end_date']);
                    });
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->withErrors([
                'overlap' => 'Данное место и время проведения уже заняты!',
            ]);
        }

        DB::beginTransaction();
        try {
            $event->name = $data['title'];
            $event->description = $data['description'];
            $event->save();

            // Обновляем расписание
            $rasp = $event->raspisanie[0];
            $rasp->start_date = $data['start_date'];
            $rasp->end_date = $data['end_date'];
            $rasp->room_id = $room->id;
            $rasp->save();

            DB::commit();

            session()->put(
                'toast',
                Toast::success(
                    'Обновлено',
                    'Событие успешно обновлено!'
                )->toArray()
            );

            return redirect()->route('event.edit', [$room->office_id, $room->id, $event->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating event: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     * @throws \Throwable
     */
    public function destroy(Event $event): \Illuminate\Http\RedirectResponse
    {
        //TODO сделать обработку исключений
        if ($event->user_id == Auth::id()) {
            $event->deleteOrFail();
            session()->put(
                'toast',
                Toast::success(
                    'Удалено',
                    'Удаление прошло успешно!'
                )->toArray()
            );
            return redirect('/offices');

        } else {
            session()->put(
                'toast',
                Toast::construct_array(
                    'Ошибка',
                    'Это событие вам не принадлежит',
                    ToastTheme::DANGER
                )
            );
            return $this->redirect_show($event);
        }
    }
}

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
use App\Services\EventService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService
    ) {}

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

        if ($this->eventService->checkEventOverlap($room, $data['start_date'], $data['end_date'])) {
            return redirect()->back()->withErrors([
                'overlap' => 'Данное место и время проведения уже заняты!',
            ]);
        }

        DB::beginTransaction();
        try {
            $event = $this->eventService->createEvent($data, Auth::id());

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
     * Get event data for API response
     */
    public function getEventData(Event $event): \Illuminate\Http\JsonResponse
    {
        $event->load(['user', 'raspisanie']);
        $schedule = $event->raspisanie->first();
        
        if (!$schedule) {
            throw new \RuntimeException('Event schedule not found');
        }

        return response()->json([
            'id' => $event->id,
            'title' => $event->name,
            'description' => $event->description,
            'start' => $schedule->start_date,
            'end' => $schedule->end_date,
            'user' => [
                'id' => $event->user->id,
                'name' => $event->user->name,
            ],
            'room_id' => $schedule->room_id,
            'created_at' => $event->created_at,
            'updated_at' => $event->updated_at,
        ]);
    }

    public function redirect_edit(Event $event): \Illuminate\Http\RedirectResponse
    {
        $room = $event->rooms->first();
        if (!$room) {
            throw new \RuntimeException('Event has no rooms');
        }
        return redirect()->route('event.edit', [$room->office_id, $room->id, $event->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws NotFoundHttpException
     */
    public function edit(Office $office, Room $room, Event $event): Response
    {
        $this->validateRoomAndEvent($office, $room, $event);
        
        $event->load('user');
        $this->loadEventSchedule($event);

        return Inertia::render('EditEventPage', [
            'event' => $event,
            'room' => $room
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $room = Room::query()->findOrFail($data['room_id']);

        if ($this->eventService->checkEventOverlap($room, $data['start_date'], $data['end_date'], $event->id)) {
            return redirect()->back()->withErrors([
                'overlap' => 'Данное место и время проведения уже заняты!',
            ]);
        }

        $this->eventService->updateEvent($event, $data);

        session()->put(
            'toast',
            Toast::success(
                'Обновлено',
                'Событие успешно обновлено!'
            )->toArray()
        );

        return redirect()->route('event.edit', [$room->office_id, $room->id, $event->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Throwable
     */
    public function destroy(Event $event): \Illuminate\Http\RedirectResponse
    {
        if ($event->user_id !== Auth::id()) {
            session()->put(
                'toast',
                Toast::construct_array(
                    'Ошибка',
                    'Это событие вам не принадлежит',
                    ToastTheme::DANGER
                )
            );
            return $this->redirect_edit($event);
        }

        $this->eventService->deleteEvent($event);

        session()->put(
            'toast',
            Toast::success(
                'Удалено',
                'Удаление прошло успешно!'
            )->toArray()
        );

        return redirect('/offices');
    }

    /**
     * Validate that room belongs to office and event belongs to room
     *
     * @throws NotFoundHttpException
     */
    private function validateRoomAndEvent(Office $office, Room $room, Event $event): void
    {
        if ($office->id !== $room->office_id) {
            abort(404, 'Данная комната не принадлежит данному офису');
        }
        if (!$event->rooms->contains($room)) {
            abort(404, 'Событие не принадлежит указанной комнате');
        }
    }

    /**
     * Load event schedule data
     */
    private function loadEventSchedule(Event $event): void
    {
        $schedule = $event->raspisanie->first();
        if ($schedule) {
            $event->start = $schedule->start_date;
            $event->end = $schedule->end_date;
        }
    }
}

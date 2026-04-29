<?php

namespace App\Orchid\Screens\Room;

use App\Models\Room;
use App\Orchid\Layouts\DeletedRoomListLayout;
use App\Orchid\Layouts\RoomListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;

class DeletedRoomScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'rooms' => Room::onlyTrashed()->paginate()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'DeletedRoomScreen';
    }

    public function description(): ?string
    {
        return 'Список удаленных комнат';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Назад к списку комнат')
                ->icon('arrow-left')
                ->route('platform.room.list'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [DeletedRoomListLayout::class];
    }

    public function restore(int $roomId): void
    {
        $room = Room::onlyTrashed()->findOrFail($roomId);
        $room->restore();
        Alert::info('Комната восстановлена');
    }

    public function forceDelete(int $roomId): void
    {
        $room = Room::onlyTrashed()->findOrFail($roomId);

        $events = $room->events()->get();

        if ($events->isEmpty()) {
            $room->forceDelete();
            Alert::info('Комната окончательно удалена');
        } else {


            Alert::view('components.BadEventsList', Color::ERROR, [
                'events' => $events,
            ]);
        }
    }
}

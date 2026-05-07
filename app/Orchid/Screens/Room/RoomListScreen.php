<?php

namespace App\Orchid\Screens\Room;

use App\Models\Room;
use App\Orchid\Layouts\RoomListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class RoomListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'rooms' => Room::paginate()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('room.list_title');
    }

    /**
     * The screen's description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('room.list_description');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('room.create_new'))
                ->icon('pencil')
                ->route('platform.room.edit'),
            Link::make(__('room.deleted_rooms'))->route('platform.room.deleted')

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [RoomListLayout::class];
    }
}

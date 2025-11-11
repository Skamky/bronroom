<?php

namespace App\Orchid\Layouts;

use App\Models\Room;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;

class RoomListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'rooms';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id')
                ->render(function (Room $room) {
                    return Link::make($room->id)
                        ->route('platform.room.edit', $room);
                }),

            TD::make('name')
                ->render(function (Room $room) {
                    return Link::make($room->name)
                        ->route('platform.room.edit', $room);
                }),

            TD::make('capacity'),
            TD::make('description'),

            TD::make('office_id', 'Office')
                ->render(function (Room $room) {
                    if ($room->office) {
                        return Link::make($room->office->name)
                            ->route('platform.office.edit', $room->office);
                    }
                    return '-';
                }),
            TD::make('created_at'),
            TD::make('updated_at')

        ];
    }
}

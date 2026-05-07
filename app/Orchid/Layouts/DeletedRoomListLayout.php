<?php

namespace App\Orchid\Layouts;

use App\Models\Room;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;

class DeletedRoomListLayout extends RoomListLayout
{
    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [...parent::columns(),
            TD::make('deleted_at', __('room.column_deleted_at')),
            TD::make(__('room.column_actions'))
                ->align(TD::ALIGN_CENTER)
                ->render(function (Room $room) {
                    return Group::make([
                        Button::make(__('room.restore_button'))
                            ->icon('refresh')
                            ->method('restore', ['roomId' => $room->id])
                            ->confirm(__('room.restore_confirm')),

                        Button::make(__('room.delete_button'))
                            ->icon('trash')
                            ->method('forceDelete', ['roomId' => $room->id])
                            ->confirm(__('room.delete_confirm')),
                    ]);
                }),
        ];
    }
}

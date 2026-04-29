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
            TD::make('deleted_at', 'Delete'),
            TD::make('Действия')
                ->align(TD::ALIGN_CENTER)
                ->render(function (Room $room) {
                    return Group::make([
                        Button::make('Восстановить')
                            ->icon('refresh')
                            ->method('restore', ['roomId' => $room->id])
                            ->confirm('Восстановить эту комнату?'),

                        Button::make('Стереть')
                            ->icon('trash')
                            ->method('forceDelete', ['roomId' => $room->id])
                            ->confirm('Окончательно удалить комнату? Это действие нельзя отменить.'),
                    ]);
                }),
        ];
    }
}

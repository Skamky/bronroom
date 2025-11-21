<?php

namespace App\Orchid\Layouts;

use App\Models\Office;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DeletedOfficeListLayout extends OfficeListLayout
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
                ->render(function (Office $office) {
                    return Group::make([
                        Button::make('Восстановить')
                            ->icon('refresh')
                            ->method('restore', ['officeId' => $office->id])
                            ->confirm('Восстановить этот офис?'),

                        Button::make('Стереть')
                            ->icon('trash')
                            ->method('forceDelete', ['officeId' => $office->id])
                            ->confirm('Окончательно удалить офис? Это действие нельзя отменить.'),
                    ]);
                }),
        ];
    }
}

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
            TD::make('deleted_at', __('office.column_deleted_at')),
            TD::make(__('office.column_actions'))
                ->align(TD::ALIGN_CENTER)
                ->render(function (Office $office) {
                    return Group::make([
                        Button::make(__('office.restore_button'))
                            ->icon('refresh')
                            ->method('restore', ['officeId' => $office->id])
                            ->confirm(__('office.restore_confirm')),

                        Button::make(__('office.delete_button'))
                            ->icon('trash')
                            ->method('forceDelete', ['officeId' => $office->id])
                            ->confirm(__('office.delete_confirm')),
                    ]);
                }),
        ];
    }
}

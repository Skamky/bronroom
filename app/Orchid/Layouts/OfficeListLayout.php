<?php

namespace App\Orchid\Layouts;

use App\Models\Office;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OfficeListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'offices';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        //TODO добавить все поля
        return [
            TD::make('id'),
            TD::make('name', 'Name')
                ->render(function (Office $office) {
                    return Link::make($office->name)->route('platform.office.edit', $office);
                }),
            TD::make('timezone'),
            TD::make('location', 'Location'),
            TD::make('start_time', 'Start Date'),
            TD::make('end_time', 'End Date'),

            TD::make('created_at', 'Created'),
            TD::make('updated_at', 'Last edit'),

        ];
    }
}

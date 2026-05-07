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
        return [
            TD::make('id'),
            TD::make('name', __('office.column_name'))
                ->render(function (Office $office) {
                    return Link::make($office->name)->route('platform.office.edit', $office);
                }),
            TD::make('timezone'),
            TD::make('location', __('office.column_location')),
            TD::make('start_time', __('office.column_start_time')),
            TD::make('end_time', __('office.column_end_time')),

            TD::make('created_at', __('office.column_created_at')),
            TD::make('updated_at', __('office.column_updated_at')),

        ];
    }

    protected function striped(): bool
    {
        return true;
    }

}

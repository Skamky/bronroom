<?php

namespace App\Orchid\Screens\Office;

use App\Models\Office;
use App\Orchid\Layouts\OfficeListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class OfficeListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'offices' => Office::paginate()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'OfficeListScreen';
    }

    public function description(): ?string
    {
        return 'All office Office description';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Create new')
                ->icon('pencil')
                ->route('platform.office.edit')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [OfficeListLayout::class];
    }
}

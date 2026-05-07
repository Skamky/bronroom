<?php

namespace App\Orchid\Screens\Office;

use App\Models\Office;
use App\Orchid\Layouts\DeletedOfficeListLayout;
use App\Orchid\Layouts\OfficeListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;

class DeletedOfficeScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'offices' => Office::onlyTrashed()->paginate()

        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('office.deleted_title');
    }

    public function description(): ?string
    {
        return __('office.deleted_description');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('office.back_to_list'))
                ->icon('arrow-left')
                ->route('platform.office.list'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [DeletedOfficeListLayout::class];
    }

    public function restore(int $officeId): void
    {
        $office = Office::onlyTrashed()->findOrFail($officeId);
        $office->restore();
        Alert::info(__('office.restored_message'));
    }

    public function forceDelete(int $officeId): void
    {
        $office = Office::onlyTrashed()->findOrFail($officeId);

        $rooms = $office->rooms()->withTrashed()->get();

        if ($rooms->isEmpty()) {
            $office->forceDelete();
            Alert::info(__('office.permanently_deleted_message'));
        } else {
            // Separate rooms into active and deleted
            $active_rooms = [];
            $trashed_rooms = [];

            foreach ($rooms as $room) {
                if ($room->trashed()) {
                    $trashed_rooms[$room->id] = $room->name;
                } else {
                    $active_rooms[$room->id] = $room->name;
                }
            }

            Alert::view('components.BadRoomsList', Color::WARNING, [
                'active_rooms' => $active_rooms,
                'trashed_rooms' => $trashed_rooms
            ]);
        }
    }
//TODO проверить и доедлать удаление востоналение
}

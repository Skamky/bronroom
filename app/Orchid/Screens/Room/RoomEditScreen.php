<?php

namespace App\Orchid\Screens\Room;

use App\Models\Room;
use App\Models\Office;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;

class RoomEditScreen extends Screen
{
    /**
     * @var Room
     */
    public $room;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Room $room): iterable
    {
        return [
            'room' => $room
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->room->exists ? __('room.edit_title') : __('room.create_title');
    }

    /**
     * The screen's description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('room.description');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('room.create_button'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->room->exists),

            Button::make(__('room.update_button'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->room->exists),

            Button::make(__('room.remove_button'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->room->exists),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('room.name')
                    ->title(__('room.name_label'))
                    ->placeholder(__('room.name_placeholder'))
                    ->help(__('room.name_help'))
                    ->required(),

                Input::make('room.capacity')
                    ->title(__('room.capacity_label'))
                    ->type('number')
                    ->placeholder(__('room.capacity_placeholder'))
                    ->help(__('room.capacity_help'))
                    ->required(),

                Input::make('room.description')
                    ->title(__('room.description_label'))
                    ->placeholder(__('room.description_placeholder'))
                    ->help(__('room.description_help')),

                Select::make('room.office_id')
                    ->title(__('room.office_label'))
                    ->fromModel(Office::class, 'name')
                    ->help(__('room.office_help'))
                    ->required(),

            ])
        ];
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->room->fill($request->get('room'))->save();

        Alert::info(__('room.saved_message'));

        return redirect()->route('platform.room.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * TODO решить проблему удаления комнат с событиями
     */
    public function remove()
    {
        $this->room->delete();

        Alert::info(__('room.deleted_message'));

        return redirect()->route('platform.room.list');
    }
}

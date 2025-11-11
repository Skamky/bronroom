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
        return $this->room->exists ? 'Edit room' : 'Creating a new room';
    }

    /**
     * The screen's description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Комнаты офиса';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Create room')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->room->exists),

            Button::make('Update')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->room->exists),

            Button::make('Remove')
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
                    ->title('Name')
                    ->placeholder('Room name')
                    ->help('Название комнаты')
                    ->required(),

                Input::make('room.capacity')
                    ->title('Capacity')
                    ->type('number')
                    ->placeholder('Number of people')
                    ->help('Вместимость комнаты')
                    ->required(),

                Input::make('room.description')
                    ->title('Description')
                    ->placeholder('Room description')
                    ->help('Описание комнаты'),

                Select::make('room.office_id')
                    ->title('Office')
                    ->fromModel(Office::class, 'name')
                    ->help('Выберите офис для этой комнаты')
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

        Alert::info('You have successfully saved a room.');

        return redirect()->route('platform.room.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * TODO решить проблему удаления комнат с событиями
     */
    public function remove()
    {
        $this->room->delete();

        Alert::info('You have successfully deleted the room.');

        return redirect()->route('platform.room.list');
    }
}

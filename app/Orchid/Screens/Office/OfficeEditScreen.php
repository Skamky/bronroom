<?php

namespace App\Orchid\Screens\Office;

use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\TimeZone;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class OfficeEditScreen extends Screen
{

    /**
     * @var Office
     */
    public $office;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Office $office): iterable
    {
        return [
            'office' => $office
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->office->exists ? 'Edit office' : 'Creating a new office';
    }

    public function description(): ?string
    {
        return 'Оффисы компании';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Create office')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->office->exists),

            Button::make('Update')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->office->exists),

            Button::make('Remove')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->office->exists),
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
                Input::make('office.name')
                    ->title('Name')
                    ->placeholder('Attractive but mysterious title')
                    ->help('Название оффиса')->required(),

                TimeZone::make('office.timezone')->required(),

                DateTimer::make('office.start_time')
                    ->title('Opening time')
                    ->noCalendar()
                    ->format24hr()
                    ->format('H:i:s')
                    ->allowInput()
                    ->required()
                    ->help('Время открытия по UTC'),

                DateTimer::make('office.end_time')
                    ->title('Closed time')
                    ->noCalendar()
                    ->format24hr()
                    ->format('H:i:s')
                    ->allowInput()
                    ->required()
                    ->help('Время закрытия по UTC'),

                Input::make('office.location')->title('Location'),
                Input::make('office.description')->title('Description'),


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
        $this->office->fill($request->get('office'))->save();

        Alert::info('You have successfully saved a office.');

        return redirect()->route('platform.office.list');
    }



    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    //TODO разобраться с удалнием оффисов при связанных комантах

    public function remove()
    {
        $this->office->delete();

        Alert::info('You have successfully deleted the office.');

        return redirect()->route('platform.office.list');
    }
}

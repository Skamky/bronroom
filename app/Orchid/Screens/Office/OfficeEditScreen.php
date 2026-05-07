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
        return $this->office->exists ? __('office.edit_title') : __('office.create_title');
    }

    public function description(): ?string
    {
        return __('office.description');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('office.create_button'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->office->exists),

            Button::make(__('office.update_button'))
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->office->exists),

            Button::make(__('office.remove_button'))
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
                    ->title(__('office.name_label'))
                    ->placeholder(__('office.name_placeholder'))
                    ->help(__('office.name_label'))->required(),

                TimeZone::make('office.timezone')->required(),

                DateTimer::make('office.start_time')
                    ->title(__('office.opening_time_label'))
                    ->noCalendar()
                    ->format24hr()
                    ->format('H:i:s')
                    ->allowInput()
                    ->required()
                    ->help(__('office.opening_time_help')),

                DateTimer::make('office.end_time')
                    ->title(__('office.closing_time_label'))
                    ->noCalendar()
                    ->format24hr()
                    ->format('H:i:s')
                    ->allowInput()
                    ->required()
                    ->help(__('office.closing_time_help')),

                Input::make('office.location')->title(__('office.location_label')),
                Input::make('office.description')->title(__('office.description_label')),


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

        Alert::info(__('office.saved_message'));

        return redirect()->route('platform.office.list');
    }



    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    //TODO разобраться с удалнием оффисов при связанных комантах

    public function remove()
    {
        $this->office->delete();

        Alert::info(__('office.deleted_message'));

        return redirect()->route('platform.office.list');
    }
}

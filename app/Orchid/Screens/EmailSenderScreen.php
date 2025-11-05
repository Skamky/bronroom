<?php

namespace App\Orchid\Screens;

use Illuminate\Http\RedirectResponse;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use App\Models\User;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

use Orchid\Support\Facades\Alert;

class EmailSenderScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'subject' => date('F') . ' Campaign News',
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Email Sender Screen';
    }

    public function description(): ?string
    {
        return "Tool that sends ad-hoc email messages.";
    }


    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Send Email')->icon('paper-plane')->method('sendMessage'),

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
                Input::make('subject')->title('Subject')->required()->placeholder('Message subject line')->help('Enter the subject line for your message'),
                Relation::make('users.')->title('Recipients')->required()->multiple()->placeholder('Email addresses ')
                    ->help('Enter the users that you would like to send this message to.')
                    ->fromModel(User::class, 'name', 'email'),
                Quill::make('content')->title('Message content')->required()->placeholder('Insert text here ...')
                    ->help('Enter the content for your message')
            ])

        ];
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'subject' => 'required|min:6|max:50',
            'users' => 'required',
            'content' => 'required|min:10'
        ]);

        Mail::raw($request->get('content'), function (Message $message) use ($request) {
            $message->from('sample@email.com');
            $message->subject($request->get('subject'));

            foreach ($request->get('users') as $email) {
                $message->to($email);
            }
        });


        Alert::info('Your email message has been sent successfully.');
    }
}

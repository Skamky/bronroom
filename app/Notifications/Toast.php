<?php

namespace App\Notifications;

use App\Enum\ToastTheme;
use Illuminate\Contracts\Support\Arrayable;

class Toast implements Arrayable
{
    protected function __construct(
        public string     $name,
        public ?string    $title,
        public ?string    $content,
        public ToastTheme $theme = ToastTheme::NORMAL,
    )
    {
    }

    public static function construct_array(string $title, ?string $message, ToastTheme $theme = ToastTheme::NORMAL): array
    {
        $name = 'toast_' . session()->increment('toast_count');
        $toast = new self(
            name: $name,
            title: $title,
            content: $message,
            theme: $theme,
        );
        return $toast->toArray();
    }

    public function toArray(): array
    {
        return (array)$this; // Автоматическое преобразование свойств
    }

    public static function success(string $title, string $message): Toast
    {
        $name = 'success_' . session()->increment('toast_count');
        return new self(
            name: $name,
            title: $title,
            content: $message,
            theme: ToastTheme::SUCCESS,
        );
    }


}

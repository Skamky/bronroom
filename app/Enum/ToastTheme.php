<?php

namespace App\Enum;

enum ToastTheme: string
{
    case NORMAL = 'normal';
    case INFO = 'info';
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case DANGER = 'danger';
    case UTILITY = 'utility';
}

<?php

namespace App\Enums;

enum StatusEnum: string
{
    case Created = 'created';
    case AtWork = 'at_work';
    case Completed = 'completed';
    case Postponed = 'postponed';
    case Cancelled = 'cancelled';

    public function label() : string
    {
        return match ($this)
        {
            self::Created => 'Создана',
            self::AtWork => 'В работе',
            self::Completed => 'Выполнена',
            self::Postponed => 'Отложена',
            self::Cancelled => 'Отменена',
        };
    }
}









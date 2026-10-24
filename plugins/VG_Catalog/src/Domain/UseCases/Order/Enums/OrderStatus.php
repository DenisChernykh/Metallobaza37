<?php

namespace VG_Catalog\Domain\UseCases\Order\Enums;

enum OrderStatus: string
{
    case PROGRESS = 'PROGRESS';
    case COMPLETED = 'COMPLETED';
    case CANCELED = 'CANCELED';
    case WAITING = 'WAITING';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public function label(): string
    {
        return match($this) {
            self::PROGRESS => 'В процессе',
            self::COMPLETED => 'Завершён',
            self::CANCELED => 'Отменён',
            self::WAITING => 'В ожидании',
        };
    }
}
<?php

declare(strict_types=1);

namespace App\Modules\Reports\Domain\Enums;

enum ReportProcessStatus: string
{
    case Started    = 'started';
    case Processing = 'processing';
    case Completed  = 'completed';
    case Failed     = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Started    => 'Запуск',
            self::Processing => 'Обработка',
            self::Completed  => 'Завершён',
            self::Failed     => 'Ошибка',
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}

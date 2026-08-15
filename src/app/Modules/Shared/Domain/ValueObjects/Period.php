<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\ValueObjects;

use App\Modules\Shared\Domain\Exceptions\InvalidValueObjectData;
use DateTimeImmutable;

final readonly class Period
{
    public function __construct(
        private DateTimeImmutable $from,
        private DateTimeImmutable $to,
    ) {
        if ($this->from > $this->to) {
            throw new InvalidValueObjectData(
                valueObject: self::class,
                field:       'range',
                value:       sprintf('%s - %s', $this->from->format(format: DATE_ATOM), $this->to->format(format: DATE_ATOM)),
                reason:      'Period start must not be later than period end.',
            );
        }
    }

    public static function between(DateTimeImmutable $from, DateTimeImmutable $to): self
    {
        return new self($from, $to);
    }

    public function from(): DateTimeImmutable
    {
        return $this->from;
    }

    public function to(): DateTimeImmutable
    {
        return $this->to;
    }

    public function equals(self $other): bool
    {
        return $this->from == $other->from && $this->to == $other->to;
    }
}

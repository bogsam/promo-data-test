<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\ValueObjects;

use App\Modules\Shared\Domain\Exceptions\InvalidValueObjectData;
use Stringable;

final readonly class Id implements Stringable
{
    public function __construct(private int $value)
    {
        if ($value <= 0) {
            throw new InvalidValueObjectData(
                valueObject: self::class,
                field: 'value',
                value: $value,
                reason: 'Identifier must be a positive integer.',
            );
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}

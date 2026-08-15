<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\ValueObjects;

use App\Modules\Shared\Domain\Exceptions\InvalidValueObjectData;
use Stringable;

final readonly class Money implements Stringable
{
    public function __construct(private int $amount) {
        if ($amount < 0) {
            throw new InvalidValueObjectData(
                valueObject: self::class,
                field:       'amount',
                value:       $amount,
                reason:      'Amount must be positive number.',
            );
        }
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount;
    }

    public function __toString(): string
    {
        return  sprintf('%.2f', $this->amount / 100);
    }
}

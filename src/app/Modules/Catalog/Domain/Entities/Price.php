<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Id;
use App\Modules\Shared\Domain\ValueObjects\Money;
use DateTimeImmutable;

final class Price
{
    private function __construct(
        private ?Id               $id,
        private Id                $productId,
        private Money             $amount,
        private DateTimeImmutable $priceDate,
    ) {}

    public static function create(
        Id                $productId,
        Money             $amount,
        DateTimeImmutable $priceDate,
    ): self {
        return new self(
            id:        null,
            productId: $productId,
            amount:    $amount,
            priceDate: $priceDate,
        );
    }

    public static function restore(
        Id                $id,
        Id                $productId,
        Money             $price,
        DateTimeImmutable $priceDate,
    ): self {
        return new self(
            id:         $id,
            productId:  $productId,
            amount:      $price,
            priceDate:  $priceDate,
        );
    }

    public function id(): ?Id
    {
        return $this->id;
    }

    public function productId(): Id
    {
        return $this->productId;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function priceDate(): DateTimeImmutable
    {
        return $this->priceDate;
    }
}

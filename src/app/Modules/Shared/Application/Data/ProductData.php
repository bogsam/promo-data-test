<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Data;

use DateTimeImmutable;

final readonly class ProductData
{
    public function __construct(
        public int $manufacturerId,
        public string $manufacturerName,
        public string $productName,
        public string $price,
        public DateTimeImmutable $priceDate,
    ) {}
}

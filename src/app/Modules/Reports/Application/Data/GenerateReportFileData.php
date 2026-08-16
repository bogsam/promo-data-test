<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\Data;

use App\Modules\Shared\Application\Data\ProductData;
use DateTimeImmutable;

final readonly class GenerateReportFileData
{
    /**
     * @param  list<ProductData>  $productPriceData
     */
    public function __construct(
        public int $processId,
        public int $categoryId,
        public int $manufacturerId,
        public DateTimeImmutable $startedAt,
        public array $productPriceData,
    ) {}
}

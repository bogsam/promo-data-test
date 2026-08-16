<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Contracts;

use App\Modules\Shared\Application\Data\ProductData;
use App\Modules\Shared\Domain\ValueObjects\Id;
use App\Modules\Shared\Domain\ValueObjects\Period;

interface CatalogReader
{
    /**
     * @return list<ProductData>
     */
    public function findProductDataByCategoryAndPeriod(Id $categoryId, Period $period): array;
}

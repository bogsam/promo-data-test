<?php

namespace App\Modules\Shared\Contracts;

use App\Modules\Shared\Data\ProductData;
use App\Modules\Shared\ValueObjects\Id;
use App\Modules\Shared\ValueObjects\Period;

interface CatalogReader
{
    /**
     * @return list<ProductData>
     */
    public function findProductDataByCategoryAndPeriod(Id $categoryId, Period $period): array;
}

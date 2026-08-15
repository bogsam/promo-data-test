<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence\Repositories;

use App\Modules\Catalog\Domain\Repositories\CatalogRepository;
use App\Modules\Shared\Contracts\CatalogReader;
use App\Modules\Shared\Data\ProductData;
use App\Modules\Shared\ValueObjects\Id;
use App\Modules\Shared\ValueObjects\Money;
use App\Modules\Shared\ValueObjects\Period;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

final class EloquentCatalogRepository implements CatalogRepository, CatalogReader
{
    public function findProductDataByCategoryAndPeriod(Id $categoryId, Period $period): array
    {
        $baseQuery = DB::table('products as p')
            ->join(table: 'manufacturers as m', first: 'm.id', operator: '=', second: 'p.manufacturer_id')
            ->join(table: 'prices as pr', first: 'pr.product_id', operator: '=', second: 'p.id')
            ->where(column: 'p.category_id', operator: '=', value: $categoryId->value())
            ->whereBetween(column: 'pr.price_date', values: [$period->from(), $period->to()]);

        $minRows = (clone $baseQuery)
            ->selectRaw(expression: '
                p.id as product_id,
                p.product_name,
                m.id as manufacturer_id,
                m.manufacturer_name,
                pr.price,
                pr.price_date,
                1 as sort_order,
                row_number() over (
                    partition by p.id
                    order by pr.price asc, pr.price_date asc, pr.id asc
                ) as row_number
            ');

        $maxRows = (clone $baseQuery)
            ->selectRaw(expression: '
                p.id as product_id,
                p.product_name,
                m.id as manufacturer_id,
                m.manufacturer_name,
                pr.price,
                pr.price_date,
                2 as sort_order,
                row_number() over (
                    partition by p.id
                    order by pr.price desc, pr.price_date desc, pr.id desc
                ) as row_number
            ');

        return DB::query()
            ->fromSub(query: $minRows->unionAll(query: $maxRows), as: 'report_rows')
            ->where(column: 'row_number', operator: 1)
            ->orderBy(column: 'product_id')
            ->orderBy(column: 'sort_order')
            ->get()
            ->map(callback: static fn (object $row): ProductData => new ProductData(
                manufacturerId:   (int) $row->manufacturer_id,
                manufacturerName: (string) $row->manufacturer_name,
                productName:      (string) $row->product_name,
                price:            (string) new Money(amount: (int) $row->price),
                priceDate:        new DateTimeImmutable(datetime: (string) $row->price_date),
            ))
            ->all();
    }
}

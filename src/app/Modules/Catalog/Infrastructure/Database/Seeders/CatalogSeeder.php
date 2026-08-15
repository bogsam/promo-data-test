<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Database\Seeders;

use App\Modules\Catalog\Infrastructure\Persistence\Models\Manufacturer;
use App\Modules\Catalog\Infrastructure\Persistence\Models\Price;
use App\Modules\Catalog\Infrastructure\Persistence\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categoryProducts = [
            10 => [
                ['name' => 'Alpha Coffee Beans', 'manufacturer_id' => 0, 'prices' => [12500, 11900, 12100, 10750, 13200, 12000, 11450]],
                ['name' => 'Beta Tea Pack',      'manufacturer_id' => 0, 'prices' => [81000, 79000, 82500, 76000, 84000, 78000, 80500]],
                ['name' => 'Gamma Cocoa Mix',    'manufacturer_id' => 0, 'prices' => [15000, 14075, 15010, 14000, 15055, 14090, 15025]],
            ],
            20 => [
                ['name' => 'Delta Biscuit', 'manufacturer_id' => 1, 'prices' => [50020, 50010, 50040, 40095, 50055, 50025, 50300]],
                ['name' => 'Epsilon Jam',   'manufacturer_id' => 1, 'prices' => [90080, 90065, 90090, 90040, 100010, 90075, 90085]],
            ],
        ];

        $manufacturers = [
            'Northwind Trading',
            'Acme Goods',
        ];

        $manufacturerModels = array_map(function ($manufacturerName) {
            return Manufacturer::factory()->create(attributes: [
                'manufacturer_name' => $manufacturerName,
            ]);
        }, $manufacturers);

        $today = CarbonImmutable::now()->startOfDay();

        foreach ($categoryProducts as $categoryId => $products) {
            foreach ($products as $productData) {
                $product = Product::factory()->create(attributes: [
                    'product_name'    => $productData['name'],
                    'category_id'     => $categoryId,
                    'manufacturer_id' => $manufacturerModels[$productData['manufacturer_id']]->id,
                ]);

                foreach ($productData['prices'] as $offset => $price) {
                    Price::factory()->create(attributes: [
                        'product_id' => $product->id,
                        'price'      => $price,
                        'price_date' => $today->subDays(6 - $offset),
                    ]);
                }
            }
        }
    }
}

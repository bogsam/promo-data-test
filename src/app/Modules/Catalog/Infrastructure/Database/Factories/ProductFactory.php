<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Database\Factories;

use App\Modules\Catalog\Infrastructure\Persistence\Models\Manufacturer;
use App\Modules\Catalog\Infrastructure\Persistence\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_name'    => $this->faker->unique()->words(3, true),
            'category_id'     => $this->faker->numberBetween(int1: 1, int2: 1000),
            'manufacturer_id' => Manufacturer::factory(),
        ];
    }

    public function forCategory(int $categoryId): self
    {
        return $this->state(state: fn (): array => [
            'category_id' => $categoryId,
        ]);
    }
}

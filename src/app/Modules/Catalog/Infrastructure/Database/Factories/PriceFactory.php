<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Database\Factories;

use App\Modules\Catalog\Infrastructure\Persistence\Models\Price;
use App\Modules\Catalog\Infrastructure\Persistence\Models\Product;
use App\Modules\Shared\ValueObjects\Period;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Price>
 */
class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'price'      => $this->faker->randomFloat(nbMaxDecimals: 2, min: 1, max: 1000),
            'price_date' => $this->faker->dateTimeBetween('-14 days', 'now'),
        ];
    }

    public function withinPeriod(Period $period): self
    {
        return $this->state(state: fn (): array => [
            'price_date' => $this->faker->dateTimeBetween(
                $period->from()->format(format: 'Y-m-d H:i:s'),
                $period->to()->format(format: 'Y-m-d H:i:s'),
            ),
        ]);
    }

    public function beforePeriod(Period $period): self
    {
        return $this->state(state: fn (): array => [
            'price_date' => $this->faker->dateTimeBetween(
                $period->from()->sub(interval: new DateInterval(duration: 'P30D'))->format(format: 'Y-m-d H:i:s'),
                $period->from()->sub(interval: new DateInterval(duration: 'PT1S'))->format(format: 'Y-m-d H:i:s'),
            ),
        ]);
    }
}

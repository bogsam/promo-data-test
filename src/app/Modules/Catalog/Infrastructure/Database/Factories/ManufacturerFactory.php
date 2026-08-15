<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Database\Factories;

use App\Modules\Catalog\Infrastructure\Persistence\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Manufacturer>
 */
class ManufacturerFactory extends Factory
{
    protected $model = Manufacturer::class;

    public function definition(): array
    {
        return [
            'manufacturer_name' => $this->faker->unique()->company(),
        ];
    }
}

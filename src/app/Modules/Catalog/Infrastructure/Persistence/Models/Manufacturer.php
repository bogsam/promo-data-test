<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence\Models;

use App\Modules\Catalog\Infrastructure\Database\Factories\ManufacturerFactory;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $manufacturer_id
 * @property string $manufacturer_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Product> $products
 * @property-read int|null $products_count
 *
 * @method static ManufacturerFactory factory($count = null, $state = [])
 * @method static Builder<static>|Manufacturer newModelQuery()
 * @method static Builder<static>|Manufacturer newQuery()
 * @method static Builder<static>|Manufacturer query()
 * @method static Builder<static>|Manufacturer whereCreatedAt($value)
 * @method static Builder<static>|Manufacturer whereId($value)
 * @method static Builder<static>|Manufacturer whereManufacturerName($value)
 * @method static Builder<static>|Manufacturer whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[UseFactory(ManufacturerFactory::class)]
class Manufacturer extends Model
{
    /** @use HasFactory<ManufacturerFactory> */
    use HasFactory;

    protected $table = 'manufacturer';

    protected $primaryKey = 'manufacturer_id';

    protected $fillable = [
        'manufacturer_name',
    ];

    /**
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(related: Product::class, foreignKey: 'manufacturer_id', localKey: 'manufacturer_id');
    }
}

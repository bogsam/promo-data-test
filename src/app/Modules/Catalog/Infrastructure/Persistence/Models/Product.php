<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence\Models;

use App\Modules\Catalog\Infrastructure\Database\Factories\ProductFactory;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $product_name
 * @property int $category_id
 * @property int $manufacturer_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Manufacturer $manufacturer
 * @property-read Collection<int, Price> $prices
 * @property-read int|null $prices_count
 *
 * @method static ProductFactory factory($count = null, $state = [])
 * @method static Builder<static>|Product newModelQuery()
 * @method static Builder<static>|Product newQuery()
 * @method static Builder<static>|Product query()
 * @method static Builder<static>|Product whereCategoryId($value)
 * @method static Builder<static>|Product whereCreatedAt($value)
 * @method static Builder<static>|Product whereId($value)
 * @method static Builder<static>|Product whereManufacturerId($value)
 * @method static Builder<static>|Product whereProductName($value)
 * @method static Builder<static>|Product whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[UseFactory(ProductFactory::class)]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'product_name',
        'category_id',
        'manufacturer_id',
    ];

    protected $casts = [
        'category_id'     => 'integer',
        'manufacturer_id' => 'integer',
    ];

    /**
     * @return BelongsTo<Manufacturer, $this>
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(related: Manufacturer::class, foreignKey: 'manufacturer_id');
    }

    /**
     * @return HasMany<Price, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(related: Price::class, foreignKey: 'product_id');
    }
}

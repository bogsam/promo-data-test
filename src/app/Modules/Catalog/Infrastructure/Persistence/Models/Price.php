<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence\Models;

use App\Modules\Catalog\Infrastructure\Database\Factories\PriceFactory;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $price_id
 * @property int $product_id
 * @property int $price
 * @property Carbon $price_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product $product
 *
 * @method static PriceFactory factory($count = null, $state = [])
 * @method static Builder<static>|Price newModelQuery()
 * @method static Builder<static>|Price newQuery()
 * @method static Builder<static>|Price query()
 * @method static Builder<static>|Price whereCreatedAt($value)
 * @method static Builder<static>|Price whereId($value)
 * @method static Builder<static>|Price wherePrice($value)
 * @method static Builder<static>|Price wherePriceDate($value)
 * @method static Builder<static>|Price whereProductId($value)
 * @method static Builder<static>|Price whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[UseFactory(PriceFactory::class)]
class Price extends Model
{
    /** @use HasFactory<PriceFactory> */
    use HasFactory;

    protected $table = 'price';

    protected $primaryKey = 'price_id';

    protected $fillable = [
        'product_id',
        'price',
        'price_date',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'price_date' => 'datetime',
    ];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(related: Product::class, foreignKey: 'product_id', ownerKey: 'product_id');
    }
}

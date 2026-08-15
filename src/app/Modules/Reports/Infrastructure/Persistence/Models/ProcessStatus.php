<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Persistence\Models;

use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property ReportProcessStatus $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, ReportProcess> $reportProcesses
 * @property-read int|null $report_processes_count
 *
 * @method static Builder<static>|ProcessStatus newModelQuery()
 * @method static Builder<static>|ProcessStatus newQuery()
 * @method static Builder<static>|ProcessStatus query()
 * @method static Builder<static>|ProcessStatus whereCode($value)
 * @method static Builder<static>|ProcessStatus whereCreatedAt($value)
 * @method static Builder<static>|ProcessStatus whereId($value)
 * @method static Builder<static>|ProcessStatus whereName($value)
 * @method static Builder<static>|ProcessStatus whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class ProcessStatus extends Model
{
    protected $table = 'process_statuses';

    protected $fillable = [
        'code',
        'name',
    ];

    protected $casts = [
        'name' => ReportProcessStatus::class,
    ];

    /**
     * @return HasMany<ReportProcess, $this>
     */
    public function reportProcesses(): HasMany
    {
        return $this->hasMany(related: ReportProcess::class, foreignKey: 'status_id');
    }
}

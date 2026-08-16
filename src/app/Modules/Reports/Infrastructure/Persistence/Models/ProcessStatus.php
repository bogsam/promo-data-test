<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Persistence\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $ps_id
 * @property string $ps_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, ReportProcess> $reportProcesses
 * @property-read int|null $report_processes_count
 *
 * @method static Builder<static>|ProcessStatus newModelQuery()
 * @method static Builder<static>|ProcessStatus newQuery()
 * @method static Builder<static>|ProcessStatus query()
 * @method static Builder<static>|ProcessStatus whereCreatedAt($value)
 * @method static Builder<static>|ProcessStatus wherePsId($value)
 * @method static Builder<static>|ProcessStatus wherePsName($value)
 * @method static Builder<static>|ProcessStatus whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class ProcessStatus extends Model
{
    protected $table = 'process_status';

    protected $primaryKey = 'ps_id';

    protected $fillable = [
        'ps_name',
    ];

    /**
     * @return HasMany<ReportProcess, $this>
     */
    public function reportProcesses(): HasMany
    {
        return $this->hasMany(related: ReportProcess::class, foreignKey: 'ps_id', localKey: 'ps_id');
    }
}

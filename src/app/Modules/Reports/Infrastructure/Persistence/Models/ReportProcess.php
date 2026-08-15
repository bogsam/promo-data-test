<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Persistence\Models;

use App\Modules\Reports\Infrastructure\Database\Factories\ReportFactory;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pid
 * @property int $category_id
 * @property Carbon $period_from
 * @property Carbon $period_to
 * @property int $status_id
 * @property Carbon $started_at
 * @property Carbon|null $finished_at
 * @property int|null $execution_time_ms
 * @property string|null $file_name
 * @property string|null $file_path
 * @property string|null $error_message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ProcessStatus $status
 *
 * @method static ReportFactory factory($count = null, $state = [])
 * @method static Builder<static>|ReportProcess newModelQuery()
 * @method static Builder<static>|ReportProcess newQuery()
 * @method static Builder<static>|ReportProcess query()
 * @method static Builder<static>|ReportProcess whereCategoryId($value)
 * @method static Builder<static>|ReportProcess whereCreatedAt($value)
 * @method static Builder<static>|ReportProcess whereErrorMessage($value)
 * @method static Builder<static>|ReportProcess whereExecutionTimeMs($value)
 * @method static Builder<static>|ReportProcess whereFileName($value)
 * @method static Builder<static>|ReportProcess whereFilePath($value)
 * @method static Builder<static>|ReportProcess whereFinishedAt($value)
 * @method static Builder<static>|ReportProcess whereId($value)
 * @method static Builder<static>|ReportProcess wherePeriodFrom($value)
 * @method static Builder<static>|ReportProcess wherePeriodTo($value)
 * @method static Builder<static>|ReportProcess wherePid($value)
 * @method static Builder<static>|ReportProcess whereStartedAt($value)
 * @method static Builder<static>|ReportProcess whereStatusId($value)
 * @method static Builder<static>|ReportProcess whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[UseFactory(ReportFactory::class)]
class ReportProcess extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory;

    protected $table = 'report_processes';

    protected $fillable = [
        'pid',
        'category_id',
        'period_from',
        'period_to',
        'status_id',
        'started_at',
        'finished_at',
        'execution_time_ms',
        'file_name',
        'file_path',
        'error_message',
    ];

    protected $casts = [
        'category_id'       => 'integer',
        'status_id'         => 'integer',
        'period_from'       => 'datetime',
        'period_to'         => 'datetime',
        'started_at'        => 'datetime',
        'finished_at'       => 'datetime',
        'execution_time_ms' => 'integer',
    ];

    /**
     * @return BelongsTo<ProcessStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(related: ProcessStatus::class, foreignKey: 'status_id');
    }

    public function resolveRouteBindingQuery($query, $value, $field = null): Builder
    {
        return parent::resolveRouteBindingQuery($query->with(relations: 'status'), $value, $field);
    }
}

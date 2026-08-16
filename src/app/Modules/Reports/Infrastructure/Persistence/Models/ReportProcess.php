<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Persistence\Models;

use App\Modules\Reports\Infrastructure\Database\Factories\ReportFactory;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $rp_id
 * @property int $rp_pid
 * @property int $rp_category_id
 * @property Carbon $rp_period_from
 * @property Carbon $rp_period_to
 * @property int $ps_id
 * @property Carbon $rp_start_datetime
 * @property Carbon|null $rp_finish_datetime
 * @property int|null $rp_exec_time
 * @property string|null $rp_file_save_path
 * @property string|null $rp_error_message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ProcessStatus $status
 *
 * @method static ReportFactory factory($count = null, $state = [])
 * @method static Builder<static>|ReportProcess newModelQuery()
 * @method static Builder<static>|ReportProcess newQuery()
 * @method static Builder<static>|ReportProcess query()
 * @method static Builder<static>|ReportProcess whereCreatedAt($value)
 * @method static Builder<static>|ReportProcess wherePsId($value)
 * @method static Builder<static>|ReportProcess whereRpCategoryId($value)
 * @method static Builder<static>|ReportProcess whereRpErrorMessage($value)
 * @method static Builder<static>|ReportProcess whereRpExecTime($value)
 * @method static Builder<static>|ReportProcess whereRpFileSavePath($value)
 * @method static Builder<static>|ReportProcess whereRpFinishDatetime($value)
 * @method static Builder<static>|ReportProcess whereRpId($value)
 * @method static Builder<static>|ReportProcess whereRpPeriodFrom($value)
 * @method static Builder<static>|ReportProcess whereRpPeriodTo($value)
 * @method static Builder<static>|ReportProcess whereRpPid($value)
 * @method static Builder<static>|ReportProcess whereRpStartDatetime($value)
 * @method static Builder<static>|ReportProcess whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[UseFactory(ReportFactory::class)]
class ReportProcess extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory;

    protected $table = 'report_process';

    protected $primaryKey = 'rp_id';

    protected $fillable = [
        'rp_pid',
        'rp_category_id',
        'rp_period_from',
        'rp_period_to',
        'ps_id',
        'rp_start_datetime',
        'rp_finish_datetime',
        'rp_exec_time',
        'rp_file_save_path',
        'rp_error_message',
    ];

    protected $casts = [
        'rp_category_id'           => 'integer',
        'ps_id'                    => 'integer',
        'rp_period_from'           => 'datetime',
        'rp_period_to'             => 'datetime',
        'rp_start_datetime'        => 'datetime',
        'rp_finish_datetime'       => 'datetime',
        'rp_exec_time'             => 'integer',
    ];

    /**
     * @return BelongsTo<ProcessStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(related: ProcessStatus::class, foreignKey: 'ps_id', ownerKey: 'ps_id');
    }

    public function resolveRouteBindingQuery($query, $value, $field = null): EloquentBuilder
    {
        return parent::resolveRouteBindingQuery($query->with(relations: 'status'), $value, $field);
    }
}

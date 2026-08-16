<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Persistence\Repositories;

use App\Modules\Reports\Domain\Entities\ReportProcess as ReportProcessEntity;
use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Domain\Repositories\ReportProcessRepository;
use App\Modules\Reports\Infrastructure\Persistence\Models\ProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess as ReportProcessModel;
use App\Modules\Shared\Domain\ValueObjects\Id;
use App\Modules\Shared\Domain\ValueObjects\Period;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class EloquentReportProcessRepository implements ReportProcessRepository
{
    /**
     * @throws DateMalformedStringException
     */
    public function save(ReportProcessEntity $reportProcess): ReportProcessEntity
    {
        $statusId = $this->resolveStatusId(status: $reportProcess->status());
        $attributes = $this->toAttributes(reportProcess: $reportProcess, statusId: $statusId);

        if ($reportProcess->id() === null) {
            $model = new ReportProcessModel(attributes: $attributes);
        } else {
            $model = ReportProcessModel::query()->findOrFail($reportProcess->id()->value());
            $model->fill(attributes: $attributes);
        }

        $model->save();

        return $this->toDomain($model);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function findById(Id $id): ?ReportProcessEntity
    {
        $model = ReportProcessModel::query()
            ->with(relations: 'status')
            ->find(id: $id->value());

        if ($model === null) {
            return null;
        }

        return $this->toDomain(model: $model);
    }

    /**
     * @return list<ReportProcessEntity>
     */
    public function findLatest(): array
    {
        return ReportProcessModel::query()
            ->with(relations: 'status')
            ->latest(column: 'rp_start_datetime')
            ->get()
            ->map(callback: fn (ReportProcessModel $model): ReportProcessEntity => $this->toDomain(model: $model))
            ->all();
    }

    /**
     * @return array{
     *     rp_pid: int,
     *     rp_category_id: int,
     *     rp_period_from: DateTimeImmutable,
     *     rp_period_to: DateTimeImmutable,
     *     ps_id: int,
     *     rp_start_datetime: DateTimeImmutable,
     *     rp_finish_datetime: DateTimeImmutable|null,
     *     rp_exec_time: int|null,
     *     rp_file_save_path: string|null,
     *     rp_error_message: string|null
     * }
     */
    private function toAttributes(ReportProcessEntity $reportProcess, int $statusId): array
    {
        return [
            'rp_pid'               => $reportProcess->pid(),
            'rp_category_id'       => $reportProcess->categoryId()->value(),
            'rp_period_from'       => $reportProcess->period()->from(),
            'rp_period_to'         => $reportProcess->period()->to(),
            'ps_id'                => $statusId,
            'rp_start_datetime'    => $reportProcess->startedAt(),
            'rp_finish_datetime'   => $reportProcess->finishedAt(),
            'rp_exec_time'         => $reportProcess->executionTimeInSeconds() !== null
                ? $reportProcess->executionTimeInSeconds() * 1000
                : null,
            'rp_file_save_path'   => $reportProcess->filePath(),
            'rp_error_message'    => $reportProcess->errorMessage(),
        ];
    }

    /**
     * @throws DateMalformedStringException
     */
    private function toDomain(ReportProcessModel $model): ReportProcessEntity
    {
        return ReportProcessEntity::restore(
            id: new Id($model->rp_id),
            pid: $model->rp_pid,
            categoryId: new Id($model->rp_category_id),
            period: Period::between(
                from: $this->toDateTimeImmutable(value: $model->rp_period_from),
                to: $this->toDateTimeImmutable(value: $model->rp_period_to),
            ),
            status: $this->statusFromLabel(label: $model->status->ps_name),
            startedAt: $this->toDateTimeImmutable(value: $model->rp_start_datetime),
            finishedAt: $model->rp_finish_datetime !== null ? $this->toDateTimeImmutable(value: $model->rp_finish_datetime) : null,
            executionTimeInSeconds: $model->rp_exec_time !== null
                ? intdiv(num1: (int) $model->rp_exec_time, num2: 1000)
                : null,
            filePath: $model->rp_file_save_path,
            errorMessage: $model->rp_error_message,
        );
    }

    private function resolveStatusId(ReportProcessStatus $status): int
    {
        $model = ProcessStatus::query()->where('ps_name', $status->label())->first();

        if ($model === null) {
            throw new ModelNotFoundException(message: sprintf('Process status "%s" not found.', $status->label()));
        }

        return $model->ps_id;
    }

    private function statusFromLabel(string $label): ReportProcessStatus
    {
        foreach (ReportProcessStatus::cases() as $status) {
            if ($status->label() === $label) {
                return $status;
            }
        }

        throw new ModelNotFoundException(message: sprintf('Process status "%s" not found.', $label));
    }

    /**
     * @throws DateMalformedStringException
     */
    private function toDateTimeImmutable(DateTimeImmutable|DateTimeInterface|string $value): DateTimeImmutable
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        return $value instanceof DateTimeInterface
            ? DateTimeImmutable::createFromInterface(object: $value)
            : new DateTimeImmutable(datetime: $value);
    }
}

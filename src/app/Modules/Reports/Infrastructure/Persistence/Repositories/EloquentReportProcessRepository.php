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
            ->latest(column: 'started_at')
            ->get()
            ->map(callback: fn (ReportProcessModel $model): ReportProcessEntity => $this->toDomain(model: $model))
            ->all();
    }

    /**
     * @return array{
     *     pid: int,
     *     category_id: int,
     *     period_from: DateTimeImmutable,
     *     period_to: DateTimeImmutable,
     *     status_id: int,
     *     started_at: DateTimeImmutable,
     *     finished_at: DateTimeImmutable|null,
     *     execution_time_ms: int|null,
     *     file_name: string|null,
     *     file_path: string|null,
     *     error_message: string|null
     * }
     */
    private function toAttributes(ReportProcessEntity $reportProcess, int $statusId): array
    {
        return [
            'pid'               => $reportProcess->pid(),
            'category_id'       => $reportProcess->categoryId()->value(),
            'period_from'       => $reportProcess->period()->from(),
            'period_to'         => $reportProcess->period()->to(),
            'status_id'         => $statusId,
            'started_at'        => $reportProcess->startedAt(),
            'finished_at'       => $reportProcess->finishedAt(),
            'execution_time_ms' => $reportProcess->executionTimeInSeconds() !== null
                ? $reportProcess->executionTimeInSeconds() * 1000
                : null,
            'file_name'         => $reportProcess->filePath() !== null ? basename(path: $reportProcess->filePath()) : null,
            'file_path'         => $reportProcess->filePath(),
            'error_message'     => $reportProcess->errorMessage(),
        ];
    }

    /**
     * @throws DateMalformedStringException
     */
    private function toDomain(ReportProcessModel $model): ReportProcessEntity
    {
        return ReportProcessEntity::restore(
            id: new Id($model->id),
            pid: $model->pid,
            categoryId: new Id($model->category_id),
            period: Period::between(
                from: $this->toDateTimeImmutable(value: $model->period_from),
                to: $this->toDateTimeImmutable(value: $model->period_to),
            ),
            status: ReportProcessStatus::from(value: $model->status->code),
            startedAt: $this->toDateTimeImmutable(value: $model->started_at),
            finishedAt: $model->finished_at !== null ? $this->toDateTimeImmutable(value: $model->finished_at) : null,
            executionTimeInSeconds: $model->execution_time_ms !== null
                ? intdiv(num1: (int) $model->execution_time_ms, num2: 1000)
                : null,
            filePath: $model->file_path,
            errorMessage: $model->error_message,
        );
    }

    private function resolveStatusId(ReportProcessStatus $status): int
    {
        $model = ProcessStatus::query()->where('code', $status->code())->first();

        if ($model === null) {
            throw new ModelNotFoundException(message: sprintf('Process status "%s" not found.', $status->code()));
        }

        return $model->id;
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

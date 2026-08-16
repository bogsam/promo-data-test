<?php

declare(strict_types=1);

namespace App\Modules\Reports\Domain\Entities;

use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Domain\Exceptions\ReportProcessTransitionNotAllowed;
use App\Modules\Shared\Domain\ValueObjects\Id;
use App\Modules\Shared\Domain\ValueObjects\Period;
use DateTimeImmutable;
use DateTimeInterface;

final class ReportProcess
{
    private function __construct(
        private ?Id $id,
        private int $pid,
        private Id $categoryId,
        private Period $period,
        private ReportProcessStatus $status,
        private DateTimeImmutable $startedAt,
        private ?DateTimeImmutable $finishedAt = null,
        private ?int $executionTimeInSeconds = null,
        private ?string $filePath = null,
        private ?string $errorMessage = null,
    ) {}

    public static function create(
        int $pid,
        Id $categoryId,
        Period $period,
        DateTimeImmutable $startedAt,
    ): self {
        return new self(
            id: null,
            pid: $pid,
            categoryId: $categoryId,
            period: $period,
            status: ReportProcessStatus::Started,
            startedAt: $startedAt,
        );
    }

    public static function restore(
        Id $id,
        int $pid,
        Id $categoryId,
        Period $period,
        ReportProcessStatus $status,
        DateTimeImmutable $startedAt,
        ?DateTimeImmutable $finishedAt = null,
        ?int $executionTimeInSeconds = null,
        ?string $filePath = null,
        ?string $errorMessage = null,
    ): self {
        return new self(
            id: $id,
            pid: $pid,
            categoryId: $categoryId,
            period: $period,
            status: $status,
            startedAt: $startedAt,
            finishedAt: $finishedAt,
            executionTimeInSeconds: $executionTimeInSeconds,
            filePath: $filePath,
            errorMessage: $errorMessage,
        );
    }

    public function id(): ?Id
    {
        return $this->id;
    }

    public function pid(): int
    {
        return $this->pid;
    }

    public function categoryId(): Id
    {
        return $this->categoryId;
    }

    public function period(): Period
    {
        return $this->period;
    }

    public function status(): ReportProcessStatus
    {
        return $this->status;
    }

    public function startedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function finishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function executionTimeInSeconds(): ?int
    {
        return $this->executionTimeInSeconds;
    }

    public function filePath(): ?string
    {
        return $this->filePath;
    }

    public function errorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function markProcessing(): void
    {
        $this->guardTransition(allowedStatuses: [ReportProcessStatus::Started], targetStatus: ReportProcessStatus::Processing);

        $this->status = ReportProcessStatus::Processing;
    }

    public function markCompleted(string $filePath, DateTimeImmutable $finishedAt): void
    {
        $this->guardTransition(
            allowedStatuses: [ReportProcessStatus::Started, ReportProcessStatus::Processing],
            targetStatus: ReportProcessStatus::Completed,
        );

        $this->status = ReportProcessStatus::Completed;
        $this->finishedAt = $finishedAt;
        $this->executionTimeInSeconds = $this->calculateExecutionTimeInSeconds(finishedAt: $finishedAt);
        $this->filePath = $filePath;
        $this->errorMessage = null;
    }

    public function markFailed(string $errorMessage, DateTimeImmutable $finishedAt): void
    {
        $this->guardTransition(
            allowedStatuses: [ReportProcessStatus::Started, ReportProcessStatus::Processing],
            targetStatus: ReportProcessStatus::Failed,
        );

        $this->status = ReportProcessStatus::Failed;
        $this->finishedAt = $finishedAt;
        $this->executionTimeInSeconds = $this->calculateExecutionTimeInSeconds(finishedAt: $finishedAt);
        $this->filePath = null;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @param  list<ReportProcessStatus>  $allowedStatuses
     */
    private function guardTransition(array $allowedStatuses, ReportProcessStatus $targetStatus): void
    {
        if (in_array(needle: $this->status, haystack: $allowedStatuses, strict: true)) {
            return;
        }

        throw new ReportProcessTransitionNotAllowed(
            processId: $this->id?->value() ?? null,
            currentStatus: $this->status,
            targetStatus: $targetStatus,
        );
    }

    private function calculateExecutionTimeInSeconds(DateTimeInterface $finishedAt): int
    {
        return max(0, $finishedAt->getTimestamp() - $this->startedAt->getTimestamp());
    }
}

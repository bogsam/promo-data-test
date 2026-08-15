<?php

declare(strict_types=1);

namespace App\Modules\Reports\Tests\Unit;

use App\Modules\Core\Tests\TestCase;
use App\Modules\Reports\Domain\Entities\ReportProcess;
use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Domain\Exceptions\ReportProcessTransitionNotAllowed;
use App\Modules\Shared\Domain\ValueObjects\Id;
use App\Modules\Shared\Domain\ValueObjects\Period;
use DateTimeImmutable;

final class ReportProcessTest extends TestCase
{
    public function test_create_initializes_started_process(): void
    {
        $startedAt = new DateTimeImmutable('2026-08-15 10:00:00 UTC');
        $period = Period::between(
            new DateTimeImmutable('2026-08-01 00:00:00 UTC'),
            new DateTimeImmutable('2026-08-31 23:59:59 UTC'),
        );

        $process = ReportProcess::create(
            pid: 12345,
            categoryId: new Id(7),
            period: $period,
            startedAt: $startedAt,
        );

        self::assertNull($process->id());
        self::assertSame(12345, $process->pid());
        self::assertTrue($process->categoryId()->equals(new Id(7)));
        self::assertTrue($process->period()->equals($period));
        self::assertSame(ReportProcessStatus::Started, $process->status());
        self::assertSame($startedAt, $process->startedAt());
        self::assertNull($process->finishedAt());
        self::assertNull($process->executionTimeInSeconds());
        self::assertNull($process->filePath());
        self::assertNull($process->errorMessage());
    }

    public function test_restore_keeps_persisted_state(): void
    {
        $startedAt  = new DateTimeImmutable('2026-08-15 10:00:00 UTC');
        $finishedAt = new DateTimeImmutable('2026-08-15 10:07:30 UTC');
        $period = Period::between(
            new DateTimeImmutable('2026-08-01 00:00:00 UTC'),
            new DateTimeImmutable('2026-08-31 23:59:59 UTC'),
        );

        $process = ReportProcess::restore(
            id: new Id(99),
            pid: 12345,
            categoryId: new Id(7),
            period: $period,
            status: ReportProcessStatus::Completed,
            startedAt: $startedAt,
            finishedAt: $finishedAt,
            executionTimeInSeconds: 450,
            filePath: '/tmp/report.csv',
            errorMessage: null,
        );

        self::assertTrue($process->id()->equals(new Id(99)));
        self::assertSame(12345, $process->pid());
        self::assertTrue($process->categoryId()->equals(new Id(7)));
        self::assertTrue($process->period()->equals($period));
        self::assertSame(ReportProcessStatus::Completed, $process->status());
        self::assertSame($startedAt, $process->startedAt());
        self::assertSame($finishedAt, $process->finishedAt());
        self::assertSame(450, $process->executionTimeInSeconds());
        self::assertSame('/tmp/report.csv', $process->filePath());
        self::assertNull($process->errorMessage());
    }

    public function test_mark_processing_changes_status_only_from_started(): void
    {
        $process = $this->newStartedProcess();

        $process->markProcessing();

        self::assertSame(ReportProcessStatus::Processing, $process->status());
        self::assertNull($process->finishedAt());
        self::assertNull($process->executionTimeInSeconds());
        self::assertNull($process->filePath());
        self::assertNull($process->errorMessage());
    }

    public function test_mark_completed_sets_completion_fields_and_clears_error(): void
    {
        $process = $this->newStartedProcess();
        $finishedAt = new DateTimeImmutable('2026-08-15 10:04:30 UTC');

        $process->markCompleted('/tmp/report.csv', $finishedAt);

        self::assertSame(ReportProcessStatus::Completed, $process->status());
        self::assertSame($finishedAt, $process->finishedAt());
        self::assertSame(270, $process->executionTimeInSeconds());
        self::assertSame('/tmp/report.csv', $process->filePath());
        self::assertNull($process->errorMessage());
    }

    public function test_mark_failed_sets_failure_fields_and_clamps_execution_time(): void
    {
        $process = $this->newStartedProcess();
        $finishedAt = new DateTimeImmutable('2026-08-15 09:59:00 UTC');

        $process->markFailed('boom', $finishedAt);

        self::assertSame(ReportProcessStatus::Failed, $process->status());
        self::assertSame($finishedAt, $process->finishedAt());
        self::assertSame(0, $process->executionTimeInSeconds());
        self::assertNull($process->filePath());
        self::assertSame('boom', $process->errorMessage());
    }

    public function test_it_does_not_allow_processing_after_completion(): void
    {
        $process = $this->newStartedProcess();
        $process->markCompleted('/tmp/report.csv', new DateTimeImmutable('2026-08-15 10:04:30 UTC'));

        $this->expectException(ReportProcessTransitionNotAllowed::class);
        $this->expectExceptionMessage('Cannot transition report process  from Завершён to Обработка.');

        $process->markProcessing();
    }

    public function test_it_does_not_allow_completion_after_failure(): void
    {
        $process = $this->newStartedProcess();
        $process->markFailed('boom', new DateTimeImmutable('2026-08-15 10:04:30 UTC'));

        $this->expectException(ReportProcessTransitionNotAllowed::class);
        $this->expectExceptionMessage('Cannot transition report process  from Ошибка to Завершён.');

        $process->markCompleted('/tmp/report.csv', new DateTimeImmutable('2026-08-15 10:05:00 UTC'));
    }

    public function test_it_does_not_allow_failure_after_completion(): void
    {
        $process = $this->newStartedProcess();
        $process->markCompleted('/tmp/report.csv', new DateTimeImmutable('2026-08-15 10:04:30 UTC'));

        $this->expectException(ReportProcessTransitionNotAllowed::class);
        $this->expectExceptionMessage('Cannot transition report process  from Завершён to Ошибка.');

        $process->markFailed('boom', new DateTimeImmutable('2026-08-15 10:05:00 UTC'));
    }

    private function newStartedProcess(): ReportProcess
    {
        return ReportProcess::create(
            pid: 12345,
            categoryId: new Id(7),
            period: Period::between(
                new DateTimeImmutable('2026-08-01 00:00:00 UTC'),
                new DateTimeImmutable('2026-08-31 23:59:59 UTC'),
            ),
            startedAt: new DateTimeImmutable('2026-08-15 10:00:00 UTC'),
        );
    }
}

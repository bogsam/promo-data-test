<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Queues\Jobs;

use App\Modules\Reports\Application\UseCases\GenerateReportFile\GenerateReportFileRequest;
use App\Modules\Reports\Application\UseCases\GenerateReportFile\GenerateReportFileUseCase;
use DateMalformedStringException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

final class GenerateReportFileJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        public readonly int $processId,
    ) {}

    /**
     * @throws DateMalformedStringException
     * @throws Throwable
     */
    public function handle(GenerateReportFileUseCase $useCase): void
    {
        $useCase->execute(request: new GenerateReportFileRequest(processId: $this->processId));
    }
}

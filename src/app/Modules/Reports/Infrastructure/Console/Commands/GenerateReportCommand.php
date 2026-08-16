<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Console\Commands;

use App\Modules\Reports\Application\UseCases\CreateReport\CreateReportRequest;
use App\Modules\Reports\Application\UseCases\CreateReport\CreateReportUseCase;
use App\Modules\Reports\Infrastructure\Queues\Jobs\GenerateReportFileJob;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Throwable;

class GenerateReportCommand extends Command
{
    protected $signature = 'report:generate {category_id}';

    protected $description = 'Create a report process for the given category id';

    public function __construct(
        private readonly CreateReportUseCase $createReportUseCase,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $categoryId = $this->argument(key: 'category_id');

        if (! is_numeric(value: $categoryId) || (int) $categoryId <= 0) {
            $this->error(string: 'The category_id must be a positive integer.');

            return self::FAILURE;
        }

        try {
            $response = $this->createReportUseCase->execute(
                request: new CreateReportRequest(
                    pid: getmypid(),
                    categoryId: (int) $categoryId,
                    startedAt: CarbonImmutable::now()->toDateTimeImmutable(),
                ),
            );

            dispatch(job: new GenerateReportFileJob(processId: $response->processId));

            $this->info(string: sprintf(
                'Report process %s for category %d has been created with status "%s".',
                $response->processId,
                $response->categoryId,
                $response->status,
            ));

            return self::SUCCESS;

        } catch (Throwable $throwable) {
            report(exception: $throwable);
            $this->error(string: $throwable->getMessage());

            return self::FAILURE;
        }
    }
}

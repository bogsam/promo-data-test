<?php

declare(strict_types=1);

namespace App\Modules\Reports\Tests\Feature;

use App\Modules\Catalog\Infrastructure\Persistence\Models\Manufacturer;
use App\Modules\Catalog\Infrastructure\Persistence\Models\Price;
use App\Modules\Catalog\Infrastructure\Persistence\Models\Product;
use App\Modules\Core\Tests\TestCase;
use App\Modules\Reports\Application\Exceptions\ReportDataNotFoundException;
use App\Modules\Reports\Application\UseCases\GenerateReportFile\GenerateReportFileUseCase;
use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Infrastructure\Database\Seeders\ProcessStatusSeeder;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess;
use App\Modules\Reports\Infrastructure\Queues\Jobs\GenerateReportFileJob;
use Carbon\CarbonImmutable;
use DateMalformedStringException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class GenerateReportFileJobTest extends TestCase
{
    use RefreshDatabase;

    private int $categoryId;

    private CarbonImmutable $periodFrom;

    private CarbonImmutable $periodTo;

    private CarbonImmutable $startedAt;

    private ReportProcess $reportProcess;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->seed(ProcessStatusSeeder::class);

        $this->categoryId = 42;
        $this->periodFrom = CarbonImmutable::parse('2026-08-08 10:00:00');
        $this->periodTo = CarbonImmutable::parse('2026-08-15 10:00:00');
        $this->startedAt = CarbonImmutable::parse('2026-08-15 10:00:00');
        $this->reportProcess = ReportProcess::factory()->create([
            'pid' => 12345,
            'category_id' => $this->categoryId,
            'period_from' => $this->periodFrom,
            'period_to' => $this->periodTo,
            'started_at' => $this->startedAt,
        ]);
    }

    /**
     * @throws DateMalformedStringException
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function test_it_generates_report_file_and_completes_process(): void
    {
        $manufacturer = $this->createReportableProductPrices();

        $this->handleJob();

        $this->reportProcess->refresh()->load('status');

        self::assertSame(ReportProcessStatus::Completed->code(), $this->reportProcess->status->code);
        self::assertSame(
            sprintf('reports/report_%d_42_2026-08-15_10-00-00.csv', $manufacturer->id),
            $this->reportProcess->file_path,
        );
        Storage::disk('local')->assertExists($this->reportProcess->file_path);

        $content = Storage::disk('local')->get($this->reportProcess->file_path);

        self::assertStringContainsString('manufacturer_name,product_name,price,price_date', $content);
        self::assertStringContainsString('"Acme Labs","Promo Widget",1000.00,"2026-08-10 10:00:00"', $content);
        self::assertStringContainsString('"Acme Labs","Promo Widget",1500.00,"2026-08-12 10:00:00"', $content);
    }

    /**
     * @throws DateMalformedStringException
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function test_it_fails_process_when_category_has_no_reportable_data(): void
    {
        try {
            $this->handleJob();
            self::fail('Report data not found exception was not thrown.');
        } catch (ReportDataNotFoundException $exception) {
            self::assertSame('No reportable data found for category 42.', $exception->getMessage());
        }

        $this->reportProcess->refresh()->load('status');

        self::assertSame(ReportProcessStatus::Failed->code(), $this->reportProcess->status->code);
        self::assertSame('No reportable data found for category 42.', $this->reportProcess->error_message);
        self::assertNull($this->reportProcess->file_path);
        self::assertSame([], Storage::disk('local')->allFiles('reports'));
    }

    private function createReportableProductPrices(): Manufacturer
    {
        $manufacturer = Manufacturer::factory()->create([
            'manufacturer_name' => 'Acme Labs',
        ]);

        $product = Product::factory()->create([
            'manufacturer_id' => $manufacturer->id,
            'category_id' => $this->categoryId,
            'product_name' => 'Promo Widget',
        ]);

        Price::factory()->create([
            'product_id' => $product->id,
            'price' => 1000,
            'price_date' => '2026-08-10 10:00:00',
        ]);

        Price::factory()->create([
            'product_id' => $product->id,
            'price' => 1500,
            'price_date' => '2026-08-12 10:00:00',
        ]);

        return $manufacturer;
    }

    /**
     * @throws DateMalformedStringException
     * @throws Throwable
     * @throws BindingResolutionException
     */
    private function handleJob(): void
    {
        new GenerateReportFileJob(processId: $this->reportProcess->id)
            ->handle($this->app->make(GenerateReportFileUseCase::class));
    }
}

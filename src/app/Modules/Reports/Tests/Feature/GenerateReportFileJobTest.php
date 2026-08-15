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
        self::assertStringContainsString('"Acme Labs","Promo Widget",10.00,"2026-08-10 10:00:00"', $content);
        self::assertStringContainsString('"Acme Labs","Promo Widget",15.00,"2026-08-12 10:00:00"', $content);
    }

    /**
     * @throws DateMalformedStringException
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function test_it_writes_minimum_and_maximum_price_rows_for_each_product(): void
    {
        $manufacturer = Manufacturer::factory()->create([
            'manufacturer_name' => 'Acme Labs',
        ]);

        $this->createProductWithPrices(
            manufacturer: $manufacturer,
            productName:  'Promo Widget',
            prices:       [
                ['amount' => 1200, 'date' => '2026-08-09 10:00:00'],
                ['amount' => 1000, 'date' => '2026-08-10 10:00:00'],
                ['amount' => 1500, 'date' => '2026-08-12 10:00:00'],
            ],
        );
        $this->createProductWithPrices(
            manufacturer: $manufacturer,
            productName:  'Promo Gadget',
            prices:       [
                ['amount' => 9900, 'date' => '2026-08-09 10:00:00'],
                ['amount' => 8750, 'date' => '2026-08-11 10:00:00'],
                ['amount' => 10325, 'date' => '2026-08-13 10:00:00'],
            ],
        );

        $this->handleJob();

        $content = Storage::disk('local')->get($this->reportProcess->refresh()->file_path);
        $rows = array_map('str_getcsv', array_filter(explode("\n", trim($content))));

        self::assertSame([
            ['manufacturer_name', 'product_name', 'price', 'price_date'],
            ['Acme Labs', 'Promo Widget', '10.00', '2026-08-10 10:00:00'],
            ['Acme Labs', 'Promo Widget', '15.00', '2026-08-12 10:00:00'],
            ['Acme Labs', 'Promo Gadget', '87.50', '2026-08-11 10:00:00'],
            ['Acme Labs', 'Promo Gadget', '103.25', '2026-08-13 10:00:00'],
        ], $rows);
    }

    /**
     * @throws DateMalformedStringException
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function test_it_ignores_prices_outside_report_period(): void
    {
        $manufacturer = Manufacturer::factory()->create([
            'manufacturer_name' => 'Acme Labs',
        ]);

        $this->createProductWithPrices(
            manufacturer: $manufacturer,
            productName:  'Promo Widget',
            prices:       [
                ['amount' => 100, 'date' => '2026-08-07 10:00:00'],
                ['amount' => 1000, 'date' => '2026-08-10 10:00:00'],
                ['amount' => 1500, 'date' => '2026-08-12 10:00:00'],
                ['amount' => 999999, 'date' => '2026-08-16 10:00:00'],
            ],
        );

        $this->handleJob();

        $content = Storage::disk('local')->get($this->reportProcess->refresh()->file_path);
        $rows = array_map('str_getcsv', array_filter(explode("\n", trim($content))));

        self::assertSame([
            ['manufacturer_name', 'product_name', 'price', 'price_date'],
            ['Acme Labs', 'Promo Widget', '10.00', '2026-08-10 10:00:00'],
            ['Acme Labs', 'Promo Widget', '15.00', '2026-08-12 10:00:00'],
        ], $rows);
    }

    /**
     * @throws DateMalformedStringException
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function test_it_ignores_products_from_other_categories(): void
    {
        $manufacturer = Manufacturer::factory()->create([
            'manufacturer_name' => 'Acme Labs',
        ]);

        $this->createProductWithPrices(
            manufacturer: $manufacturer,
            productName:  'Promo Widget',
            prices:       [
                ['amount' => 1000, 'date' => '2026-08-10 10:00:00'],
                ['amount' => 1500, 'date' => '2026-08-12 10:00:00'],
            ],
        );
        $this->createProductWithPrices(
            manufacturer: $manufacturer,
            productName:  'Other Category Widget',
            prices:       [
                ['amount' => 100, 'date' => '2026-08-10 10:00:00'],
                ['amount' => 999999, 'date' => '2026-08-12 10:00:00'],
            ],
            categoryId:   99,
        );

        $this->handleJob();

        $content = Storage::disk('local')->get($this->reportProcess->refresh()->file_path);
        $rows = array_map('str_getcsv', array_filter(explode("\n", trim($content))));

        self::assertSame([
            ['manufacturer_name', 'product_name', 'price', 'price_date'],
            ['Acme Labs', 'Promo Widget', '10.00', '2026-08-10 10:00:00'],
            ['Acme Labs', 'Promo Widget', '15.00', '2026-08-12 10:00:00'],
        ], $rows);
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
     * @param  list<array{amount: int, date: string}>  $prices
     */
    private function createProductWithPrices(
        Manufacturer $manufacturer,
        string $productName,
        array $prices,
        ?int $categoryId = null,
    ): void {
        $product = Product::factory()->create([
            'manufacturer_id' => $manufacturer->id,
            'category_id' => $categoryId ?? $this->categoryId,
            'product_name' => $productName,
        ]);

        foreach ($prices as $price) {
            Price::factory()->create([
                'product_id' => $product->id,
                'price' => $price['amount'],
                'price_date' => $price['date'],
            ]);
        }
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

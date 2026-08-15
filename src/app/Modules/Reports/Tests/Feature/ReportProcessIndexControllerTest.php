<?php

declare(strict_types=1);

namespace App\Modules\Reports\Tests\Feature;

use App\Modules\Core\Tests\TestCase;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess;

final class ReportProcessIndexControllerTest extends TestCase
{
    public function test_it_renders_report_processes_from_the_route(): void
    {
        ReportProcess::factory(10)->create();

        $response = $this->get('/report-processes');

        $response->assertOk();
        $response->assertViewHas('processes', static function (array $items): bool {
            return count($items) === 10;
        });
    }

    public function test_it_shows_empty_state_when_no_processes_exist(): void
    {
        $response = $this->get('/report-processes');

        $response->assertOk();
        $response->assertViewHas('processes', static fn (array $items): bool => $items === []);
    }
}

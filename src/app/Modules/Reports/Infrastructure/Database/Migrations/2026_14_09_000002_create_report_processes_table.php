<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_processes', function (Blueprint $table): void {
            $table->id();
            $table->integer(column: 'pid')->unique();
            $table->unsignedBigInteger(column: 'category_id')->index();
            $table->dateTime(column: 'period_from');
            $table->dateTime(column: 'period_to');
            $table->foreignId(column: 'status_id')->constrained(table: 'process_statuses');
            $table->dateTime(column: 'started_at')->index();
            $table->dateTime(column: 'finished_at')->nullable();
            $table->unsignedInteger(column: 'execution_time_ms')->nullable();
            $table->string(column: 'file_name')->nullable();
            $table->string(column: 'file_path')->nullable();
            $table->text(column: 'error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_processes');
    }
};

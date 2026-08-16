<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_process', function (Blueprint $table): void {
            $table->id(column: 'rp_id');
            $table->integer(column: 'rp_pid')->unique();
            $table->unsignedBigInteger(column: 'rp_category_id')->index();
            $table->dateTime(column: 'rp_period_from');
            $table->dateTime(column: 'rp_period_to');
            $table->foreignId(column: 'ps_id')
                ->constrained(table: 'process_status', column: 'ps_id');
            $table->dateTime(column: 'rp_start_datetime')->index();
            $table->dateTime(column: 'rp_finish_datetime')->nullable();
            $table->unsignedInteger(column: 'rp_exec_time')->nullable();
            $table->string(column: 'rp_file_save_path')->nullable();
            $table->text(column: 'rp_error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_process');
    }
};

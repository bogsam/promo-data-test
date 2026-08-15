<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string(column: 'code')->unique();
            $table->string(column: 'name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('process_statuses');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'product_id')->constrained(table: 'products');
            $table->unsignedBigInteger(column: 'price');
            $table->dateTime(column: 'price_date')->index();
            $table->timestamps();

            $table->index(['product_id', 'price_date', 'price'], 'prices_product_date_price_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};

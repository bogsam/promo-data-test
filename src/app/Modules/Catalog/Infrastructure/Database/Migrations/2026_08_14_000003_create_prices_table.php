<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price', function (Blueprint $table): void {
            $table->id(column: 'price_id');
            $table->foreignId(column: 'product_id')
                ->constrained(table: 'product', column: 'product_id');
            $table->unsignedBigInteger(column: 'price')->comment('Price in minor units.');
            $table->dateTime(column: 'price_date')->index();
            $table->timestamps();

            $table->index(columns: ['product_id', 'price_date', 'price'], name: 'price_product_date_price_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price');
    }
};

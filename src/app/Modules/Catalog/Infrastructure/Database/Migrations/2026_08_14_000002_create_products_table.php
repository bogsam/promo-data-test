<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table): void {
            $table->id(column: 'product_id');
            $table->string(column: 'product_name');
            $table->unsignedBigInteger(column: 'category_id')->index();
            $table->foreignId(column: 'manufacturer_id')
                ->constrained(table: 'manufacturer', column: 'manufacturer_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

            // Units purchased in this line — whole units only.
            // If fractional units are ever needed (sold by weight), change to decimal(12,3).
            $table->unsignedInteger('qty');

            $table->decimal('unit_cost', 12, 2);   // what we paid per unit
            $table->decimal('sale_price', 12, 2);  // price at which this stock is offered
            $table->timestamps();

            // Composite index speeds up the FIFO allocator:
            //   SELECT ... FROM batch_items WHERE product_id = ? ORDER BY batch_id ...
            $table->index(['product_id', 'batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_items');
    }
};

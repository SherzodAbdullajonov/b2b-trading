<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            // The exact batch_item this unit was drawn from (FIFO allocator output).
            // This pin is what makes per-batch profit accounting possible.
            $table->foreignId('batch_item_id')
                ->constrained()
                ->restrictOnDelete();

            // Denormalized for quick filtering (avoid joining batch_items for simple queries).
            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

            $table->unsignedInteger('qty');

            // Price snapshot copied from batch_items.sale_price at order time.
            // Historical orders stay accurate even if the batch's sale_price is later edited.
            $table->decimal('unit_price', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

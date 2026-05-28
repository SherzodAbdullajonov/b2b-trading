<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_refund_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_refund_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('order_item_id')
                ->constrained()
                ->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->timestamps();

            // Used by the stock calculator and the per-batch profit math
            // (refunds flow back to the originating batch_item via order_items.batch_item_id).
            $table->index('order_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refund_items');
    }
};

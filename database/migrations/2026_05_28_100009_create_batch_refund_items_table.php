<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batch_refund_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_refund_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('batch_item_id')
                ->constrained()
                ->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->timestamps();

            // Used heavily by the stock calculator to subtract refunded units.
            $table->index('batch_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_refund_items');
    }
};

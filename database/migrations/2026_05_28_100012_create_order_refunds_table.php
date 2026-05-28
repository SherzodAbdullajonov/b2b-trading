<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained()
                ->restrictOnDelete();
            $table->dateTime('refunded_at');
            $table->string('reason', 500)->nullable();
            $table->timestamps();

            $table->index('refunded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
    }
};

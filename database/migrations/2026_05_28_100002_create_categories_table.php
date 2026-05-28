<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // Self-referential parent. Root categories have parent_id = NULL.
            $table->foreignId('parent_id')->nullable()
                ->constrained('categories')
                ->restrictOnDelete();

            // Only root categories set provider_id; children inherit transitively.
            $table->foreignId('provider_id')->nullable()
                ->constrained('providers')
                ->restrictOnDelete();

            $table->timestamps();
        });

        // Enforce: exactly one of (parent_id, provider_id) must be set.
        // Root => parent_id NULL, provider_id NOT NULL
        // Child => parent_id NOT NULL, provider_id NULL
        DB::statement(<<<SQL
            ALTER TABLE categories
            ADD CONSTRAINT chk_category_parent_xor_provider
            CHECK (
                (parent_id IS NULL     AND provider_id IS NOT NULL)
             OR (parent_id IS NOT NULL AND provider_id IS NULL)
            )
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

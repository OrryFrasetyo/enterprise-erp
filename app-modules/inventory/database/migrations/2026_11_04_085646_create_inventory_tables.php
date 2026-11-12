<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 150);
            $table->string('category', 50);
            $table->string('unit', 20)->default('pcs');
            $table->bigInteger('selling_price');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('branch_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->index();
            $table->foreignId('part_id')->constrained('parts')->cascadeOnDelete();
            $table->integer('quantity_on_hand')->default(0);
            $table->integer('quantity_reserved')->default(0);
            $table->integer('min_stock_threshold')->default(5);
            $table->timestamps();

            $table->unique(['branch_id', 'part_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_stock_id')->constrained('branch_stocks')->cascadeOnDelete();
            $table->string('reference_type', 30);
            $table->unsignedBigInteger('reference_id');
            $table->string('movement_type', 20);
            $table->integer('quantity');
            $table->unsignedBigInteger('actor_id')->index();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('branch_stocks');
        Schema::dropIfExists('parts');
    }
};

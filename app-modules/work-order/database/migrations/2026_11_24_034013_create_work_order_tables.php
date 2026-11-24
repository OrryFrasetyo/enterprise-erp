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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number', 30)->unique();
            $table->unsignedBigInteger('branch_id')->index();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('vehicle_id')->index();
            $table->unsignedBigInteger('service_advisor_id')->index();
            $table->unsignedBigInteger('mechanic_id')->nullable()->index();
            $table->string('inspection_mongo_id', 24)->nullable();
            $table->smallInteger('queue_number');
            $table->string('status', 30)->default('queued');
            $table->integer('odometer_in')->default(0);
            $table->text('customer_complaint');
            $table->text('mechanic_notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'status', 'created_at']);
        });

        Schema::create('work_order_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->string('service_name', 150);
            $table->bigInteger('labor_cost')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        Schema::create('work_order_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->unsignedBigInteger('part_id')->index();
            $table->string('part_sku_snapshot', 50);
            $table->string('part_name_snapshot', 150);
            $table->integer('qty');
            $table->bigInteger('unit_price_snapshot');
            $table->bigInteger('subtotal');
            $table->string('reservation_status', 20)->default('reserved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_parts');
        Schema::dropIfExists('work_order_services');
        Schema::dropIfExists('work_orders');
    }
};

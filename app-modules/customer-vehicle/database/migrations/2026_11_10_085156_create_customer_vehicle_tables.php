<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->unique();
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('license_plate', 15)->unique();
            $table->string('vin', 50)->unique();
            $table->string('engine_number', 50);
            $table->string('brand', 50);
            $table->string('model', 100);
            $table->smallInteger('year');
            $table->string('transmission_type', 20);
            $table->integer('current_odometer')->default(0);
            $table->timestamps();

            $table->index(['license_plate', 'vin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('customers');
    }
};

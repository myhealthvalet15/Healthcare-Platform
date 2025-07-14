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
        Schema::create('corporate_inventory', function (Blueprint $table) {
            $table->integer('corporate_inventory_id')->primary();
            $table->date('date');
            $table->string('equipment_name');
            $table->string('equipment_code');
            $table->integer('equipment_cost')->nullable();
            $table->string('manufacturers')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->string('equipment_lifetime')->nullable();
            $table->string('purchase_order')->nullable();
            $table->string('vendors')->nullable();
            $table->integer('in_use')->default(0);
            $table->string('corporate_id')->nullable();
            $table->string('location_id')->nullable();
            $table->date('calibrated_date')->nullable();
            $table->date('next_calibration_date')->nullable();
            $table->longText('calibration_comments');
            $table->json('calibration_history')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_inventory');
    }
};

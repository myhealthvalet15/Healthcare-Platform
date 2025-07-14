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
        Schema::create('corporate_ohc_pharmacy', function (Blueprint $table) {
            $table->integer('ohc_pharmacy_id')->primary();
            $table->string('pharmacy_name')->nullable();
            $table->string('corporate_id')->nullable();
            $table->string('location_id')->nullable();
            $table->boolean('main_pharmacy')->nullable();
            $table->boolean('active_status')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_ohc_pharmacy');
    }
};

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
        Schema::create('prescription_template', function (Blueprint $table) {
            $table->unsignedBigInteger('prescription_template_id')->primary();
            $table->string('location_id')->nullable();
            $table->string('ohc_id')->nullable();
            $table->string('pharmacy_id')->nullable();
            $table->string('template_name')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('created_by')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_template');
    }
};

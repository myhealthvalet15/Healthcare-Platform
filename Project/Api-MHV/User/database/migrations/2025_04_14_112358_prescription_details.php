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
        Schema::create('prescription_details', function (Blueprint $table) {
            $table->integer('prescription_details_id')->primary();
            $table->string('prescription_row_id')->nullable();
            $table->string('drug_name')->nullable();
            $table->integer('drug_template_id')->nullable();
            $table->integer('to_issue')->nullable();
            $table->integer('remaining_medicine')->nullable();
            $table->integer('substitute_drug')->nullable();
            $table->string('prescribed_days')->nullable();
            $table->string('early_morning')->nullable();
            $table->string('morning')->nullable();
            $table->string('late_morning')->nullable();
            $table->string('afternoon')->nullable();
            $table->string('late_afternoon')->nullable();
            $table->string('evening')->nullable();
            $table->string('night')->nullable();
            $table->string('late_night')->nullable();
            $table->string('intake_condition')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->enum('prescription_type', ['Type1', 'Type2'])->nullable();
            $table->string('alternate_drug', 100)->nullable();
            $table->integer('alternate_quantity')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_details');
    }
};

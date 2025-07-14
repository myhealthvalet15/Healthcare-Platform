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
        Schema::create('prescription_template_drugs', function (Blueprint $table) {
            $table->unsignedBigInteger('prescription_template_drugs_id')->primary();
            $table->integer('prescription_template_id');
            $table->integer('drug_template_id');
            $table->integer('intake_days')->nullable();
            $table->integer('morning')->default(0);
            $table->integer('afternoon')->default(0);
            $table->integer('evening')->default(0);
            $table->integer('night')->default(0);
            $table->integer('intake_condition')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_template_drugs');
    }
};

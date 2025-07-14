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
        Schema::create('pharmacy_stock', function (Blueprint $table) {
            $table->integer('drug_id')->primary();
            $table->integer('ohc_pharmacy_id')->nullable();
            $table->string('drug_name')->nullable();
            $table->string('drug_template_id', 56)->nullable();
            $table->string('drug_batch')->nullable();
            $table->date('manufacter_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('drug_type')->nullable();
            $table->string('drug_strength')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('current_availability')->nullable();
            $table->integer('sold_quantity')->nullable();
            $table->integer('ohc')->nullable();
            $table->integer('master_pharmacy_id')->nullable();
            $table->float('sgst')->nullable();
            $table->float('cgst')->nullable();
            $table->float('igst')->nullable();
            $table->float('amount_per_tab')->nullable();
            $table->float('total_cost')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('created_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_stock');
    }
};

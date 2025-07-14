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
        Schema::create('drug_template', function (Blueprint $table) {
            $table->integer('drug_template_id')->primary();
            $table->string('drug_name');
            $table->integer('drug_type')->nullable();
            $table->string('drug_manufacturer')->nullable();
            $table->text('drug_ingredient')->nullable();
            $table->string('corporate_id')->nullable();
            $table->string('location_id')->nullable();
            $table->string('ohc')->nullable();
            $table->integer('master_pharmacy_id')->nullable();
            $table->string('drug_strength')->nullable();
            $table->integer('restock_alert_count')->nullable();
            $table->string('crd')->nullable();
            $table->string('schedule')->nullable();
            $table->integer('id_no')->nullable();
            $table->string('hsn_code')->nullable();
            $table->float('amount_per_strip')->nullable();
            $table->string('unit_issue')->nullable();
            $table->integer('tablet_in_strip')->nullable();
            $table->float('amount_per_tab')->nullable();
            $table->float('discount')->nullable();
            $table->float('sgst')->nullable();
            $table->float('cgst')->nullable();
            $table->float('igst')->nullable();
            $table->string('bill_status')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamp('created_on')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_template');
    }
};

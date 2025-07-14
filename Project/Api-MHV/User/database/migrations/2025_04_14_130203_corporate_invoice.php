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
        Schema::create('corporate_invoice', function (Blueprint $table) {
            $table->integer('corporate_invoice_id')->primary();
            $table->string('corporate_id')->nullable();
            $table->string('location_id')->nullable();
            $table->string('corporate_user_id')->nullable();
            $table->integer('corporate_po_id')->nullable();
            $table->string('po_number');
            $table->date('invoice_date')->nullable();
            $table->string('invoice_number')->nullable();
            $table->integer('invoice_amount')->nullable();
            $table->date('entry_date')->nullable();
            $table->date('ohc_verify_date')->nullable();
            $table->date('hr_verify_date')->nullable();
            $table->string('ses_number')->nullable();
            $table->date('ses_date')->nullable();
            $table->date('head_verify_date')->nullable();
            $table->date('ses_release_date')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('cash_vendor')->nullable();
            $table->string('cash_invoice_details')->nullable();
            $table->integer('invoice_status')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_invoice');
    }
};

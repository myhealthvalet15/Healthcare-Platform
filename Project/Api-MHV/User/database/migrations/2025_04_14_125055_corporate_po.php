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
        Schema::create('corporate_po', function (Blueprint $table) {
            $table->integer('corporate_po_id')->primary();
            $table->string('corporate_id')->nullable();
            $table->string('location_id')->nullable();
            $table->string('corporate_user_id')->nullable();
            $table->string('vendor_name');
            $table->string('po_number');
            $table->integer('po_value')->nullable();
            $table->date('po_date');
            $table->timestamp('created_at')->nullable();
            $table->string('updated_at')->nullable(); // Not using timestamp for updated_at, as per schema
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_po');
    }
};

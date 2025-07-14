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
        Schema::create('drug_stock_sold', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            $table->unsignedBigInteger('pharmacy_stock_id');
            $table->integer('quantity');
            $table->string('drug_value', 255);
            $table->unsignedBigInteger('master_user_id');
            $table->unsignedBigInteger('prescription_id');
            $table->unsignedBigInteger('ohc');
            $table->unsignedBigInteger('ohc_pharmacy_id');
            $table->unsignedBigInteger('move_to');
            $table->boolean('pharmacy_walkin')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_on');
            $table->unsignedBigInteger('master_pharmacy_id');

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_stock_sold');
    }
};

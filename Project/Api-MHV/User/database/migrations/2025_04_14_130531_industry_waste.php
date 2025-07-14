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
        Schema::create('industry_waste', function (Blueprint $table) {
            $table->integer('industry_id')->primary();
            $table->date('date');
            $table->string('corp_id')->nullable();
            $table->string('loc_id')->nullable();
            $table->integer('red');
            $table->integer('yellow');
            $table->integer('blue');
            $table->integer('white');
            $table->string('issued_by');
            $table->string('received_by');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industry_waste');
    }
};

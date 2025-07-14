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
        Schema::create('ohc_components', function (Blueprint $table) {
            $table->id();
            $table->string('corporate_id');
            $table->string('location_id');
            $table->json('injury_color_types');
            $table->foreign('corporate_id')->references('corporate_id')->on('master_corporate');
            $table->foreign('location_id')->references('location_id')->on('master_corporate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ohc_components');
    }
};

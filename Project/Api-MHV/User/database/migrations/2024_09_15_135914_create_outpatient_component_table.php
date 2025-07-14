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
        Schema::create('outpatient_component', function (Blueprint $table) {
            $table->bigIncrements('op_component_id');
            $table->string('op_component_name'); 
            $table->string('op_component_type');
            $table->boolean('active_status')->default(0); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outpatient_component');
    }
};

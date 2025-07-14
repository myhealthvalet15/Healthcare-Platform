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
        Schema::create('doctor_qualification', function (Blueprint $table) {
            $table->bigIncrements('qualification_id');
            $table->string('qualification_name');
            $table->string('qualification_type');
            $table->boolean('active_status')->default(0);        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_qualification');
    }
};

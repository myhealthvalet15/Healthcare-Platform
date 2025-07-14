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
        Schema::create('corporate_forms', function (Blueprint $table) {
            $table->id('corporate_form_id');     // Custom ID field
            $table->string('form_name');         // Form name
            $table->string('state');             // State
            $table->boolean('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();                // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        Schema::dropIfExists('corporate_forms');
    }
};

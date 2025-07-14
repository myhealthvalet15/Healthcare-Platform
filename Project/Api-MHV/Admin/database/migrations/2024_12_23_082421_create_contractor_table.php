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
        Schema::create('corporate_contractors', function (Blueprint $table) {
            $table->integer('corporate_contractors_id')->primary()->index(); 
            $table->string('contractor_name');
            $table->string('email');
            $table->string('address');
            $table->string('location_id')->collation('utf8mb4_unicode_ci')->index();
            $table->string('active_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor');
    }
};

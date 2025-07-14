<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('address', function (Blueprint $table) {
            $table->string('address_id')->primary(); // Explicitly set as Primary key
            $table->string('address_name', 255)->collation('utf8mb4_unicode_ci');
            $table->string('address_type', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('country_id', 255)->index();
            $table->string('state_id', 255)->index();
            $table->string('city_id', 255)->index();
            $table->string('area_id', 255)->index();
            $table->string('active_status', 255);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address');
    }
};

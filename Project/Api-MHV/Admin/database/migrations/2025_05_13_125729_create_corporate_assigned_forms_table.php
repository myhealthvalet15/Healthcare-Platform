<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('corporate_assigned_forms', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('corporate_id'); // Alphanumeric
            $table->string('location_id');  // Alphanumeric
            $table->unsignedBigInteger('form_id'); // Integer
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_assigned_forms');
    }
};

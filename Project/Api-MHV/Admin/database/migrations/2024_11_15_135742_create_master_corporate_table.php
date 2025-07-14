<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_corporate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('corporate_id', 255)->index(); // Corporate ID
            $table->string('location_id', 255)->index(); // Location ID
            $table->string('corporate_no', 255);
            $table->string('corporate_name', 255);
            $table->string('display_name', 255);
            $table->string('registration_no', 255); // Registration Number
            $table->string('industry', 255); // Industry
            $table->string('industry_segment', 255); // Industry Segment
            $table->string('prof_image', 255)->nullable(); // Profile Image
            $table->text('company_profile')->nullable(); // Company Profile
            $table->string('gstin', 255)->nullable(); // GSTIN
            $table->string('discount', 255)->nullable(); // Discount
            $table->string('created_by', 255); // Created By
            $table->date('created_on')->nullable(); // Created On
            $table->date('valid_from')->nullable(); // Valid From
            $table->date('valid_upto')->nullable(); // Valid Upto
            $table->string('corporate_color', 255)->nullable(); // Corporate Color
            $table->boolean('active_status')->default(true); // Active Status
            $table->timestamps(); // Created_At and Updated_At columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_corporate');
    }
};

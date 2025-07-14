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
        // Schema::create('master_corporate_address', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('location_id');

        //     $table->integer('pincode_id');
        //     $table->integer('area_id');

        //     $table->string('latitude')->nullable();
        //     $table->string('longitude')->nullable();
        //     $table->string('website_link')->nullable();
        // });
        Schema::create('master_corporate_address', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('corporate_id', 255); // Foreign key referencing master_corporate
            $table->string('location_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->string('country_id', 255)->index(); // Match `unsignedInteger` in `address`
            $table->string('state_id', 255)->index(); // Match `unsignedInteger` in `address`
            $table->string('city_id', 255)->index(); // Match `unsignedInteger` in `address`
            $table->string('area_id', 255)->index(); // Match `unsignedInteger` in `address`
            $table->string('pincode_id', 255)->index(); // Match `unsignedInteger` in `address`
            $table->string('latitude', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->string('website_link', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->timestamps();
            // Foreign keys
            // $table->foreign('corporate_id')
            //     ->references('corporate_id')
            //     ->on('master_corporate')
            //     ->onDelete('cascade');

            $table->foreign('country_id')
                ->references('country_id')
                ->on('address')
                ->onDelete('cascade');

            $table->foreign('state_id')
                ->references('state_id')
                ->on('address')
                ->onDelete('cascade');

            $table->foreign('city_id')
                ->references('city_id')
                ->on('address')
                ->onDelete('cascade');

            $table->foreign('pincode_id')
            ->references('address_id')
            ->on('address')
            ->onDelete('cascade');


            $table->foreign('area_id')
                ->references('area_id')
                ->on('address')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_corporate_address');
    }
};

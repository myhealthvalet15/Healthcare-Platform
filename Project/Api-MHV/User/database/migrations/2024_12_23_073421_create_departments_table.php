<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Create corporate_hl1 table
        Schema::create('corporate_hl1', function (Blueprint $table) {
            $table->bigIncrements('hl1_id')->index(); // Auto-increment primary key with index
            $table->string('corporate_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->string('location_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->string('corporate_admin_user_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->string('hl1_name');
            $table->string('hl1_code');
            $table->boolean('active_status')->default(0);
            $table->timestamps();

            $table->foreign('corporate_id')->references('corporate_id')->on('master_corporate')->onDelete('cascade');
            $table->foreign('corporate_admin_user_id')->references('corporate_admin_user_id')->on('corporate_admin_user')->onDelete('cascade');
        });

        Schema::create('corporate_hl2', function (Blueprint $table) {
            $table->bigIncrements('hl2_id')->index(); // Auto-increment primary key with index
            $table->string('hl2_name');
            $table->string('hl2_code');
            $table->text('description')->nullable();
            $table->bigInteger('hl1_id')->unsigned();
            $table->boolean('active_status')->default(true);
            $table->string('corporate_admin_user_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('corporate_admin_user_id')->references('corporate_admin_user_id')->on('corporate_admin_user')->onDelete('cascade');
            $table->foreign('hl1_id')->references('hl1_id')->on('corporate_hl1')->onDelete('cascade');

            // Add index to hl1_id
        });

        // Create corporate_hl3 table
        Schema::create('corporate_hl3', function (Blueprint $table) {
            $table->bigIncrements('hl3_id')->index(); // Auto-increment primary key with index
            $table->string('hl3_name');
            $table->string('h13_code');
            $table->bigInteger('hl2_id')->unsigned(); // BIGINT foreign key
            $table->boolean('active_status')->default(0);
            $table->string('corporate_admin_user_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('hl2_id')->references('hl2_id')->on('corporate_hl2')->onDelete('cascade');
            $table->foreign('corporate_admin_user_id')->references('corporate_admin_user_id')->on('corporate_admin_user')->onDelete('cascade');

            // Add index to hl2_id and hl3_id
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop tables in reverse order to avoid foreign key issues
        Schema::dropIfExists('corporate_hl3');
        Schema::dropIfExists('corporate_hl2');
        Schema::dropIfExists('corporate_hl1');
    }
};

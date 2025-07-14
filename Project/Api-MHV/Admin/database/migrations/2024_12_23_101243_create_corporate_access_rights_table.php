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
        Schema::create('corporate_access_rights', function (Blueprint $table) {
            $table->bigIncrements('id')->index(); 
            $table->string('corporate_admin_user_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->string('location_id', 255)->collation('utf8mb4_unicode_ci')->index(); 
            $table->bigInteger('hl1_id')->unsigned();
            $table->boolean('department_all')->default(0);
            $table->boolean('location_admin')->default(0);
            $table->boolean('mhc_rights')->default(0);
            $table->boolean('ohc_rights')->default(0);
            $table->integer('inserted_by')->unsigned();
            $table->timestamp('inserted_date')->useCurrent();
            $table->boolean('active_status')->default(0);

            $table->timestamps();
            $table->foreign('hl1_id')->references('hl1_id')->on('corporate_hl1')->onDelete('cascade');

            $table->foreign('corporate_admin_user_id')->references('corporate_admin_user_id')->on('corporate_admin_user')->onDelete('cascade');
            $table->foreign('location_id')->references('location_id')->on('master_corporate')->onDelete('cascade'); // Updated reference
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_access_rights_tables');
    }
};

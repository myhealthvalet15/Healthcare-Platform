<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  

    public function up()
    {
        // Create corporate_menu_rights table
        Schema::create('corporate_menu_rights', function (Blueprint $table) {
            $table->bigIncrements('id')->index();// Auto-increment primary key
            $table->string('corporate_admin_user_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->string('location_id', 255)->collation('utf8mb4_unicode_ci')->index();

           
            $table->enum('landing_page', [1, 2, 3]);
            $table->enum('employees', [0, 1, 2]);
            $table->enum('employee_monitoring', [0, 1]);
            $table->enum('diagnostic_assessment', [0, 1, 2]);
            $table->enum('hra', [0, 1, 2]);
            $table->enum('stress_management', [0, 1, 2]);
            $table->enum('pre_employment', [0, 1, 2]);
            $table->enum('reports', [0, 1]);
            $table->enum('events', [1, 2]);
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('corporate_admin_user_id')->references('corporate_admin_user_id')->on('corporate_admin_user')->onDelete('cascade');
            $table->foreign('location_id')->references('location_id')->on('master_corporate')->onDelete('cascade');
        });

        // Create second table with additional columns
        Schema::create('ohc_menu_rights', function (Blueprint $table) {
            $table->bigIncrements('id')->index();// Auto-increment primary key
            $table->string('corporate_admin_user_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->string('location_id', 255)->collation('utf8mb4_unicode_ci')->index();
            $table->boolean('doctor')->default(0);
            $table->unsignedBigInteger('qualification_id');
            $table->unsignedBigInteger('pharmacy_id');
            $table->boolean('ohc_dashboard')->default(0);
            $table->enum('out_patient', [0, 1, 2]);
            $table->enum('prescription', [0, 1, 2]);
            $table->enum('tests', [0, 1, 2]);
            $table->enum('stocks', [0, 1, 2]);
            $table->boolean('ohc_report')->default(0);
            $table->boolean('census_report')->default(0);
            $table->enum('safety_board', [0, 1, 2]);
            $table->enum('invoice', [0, 1, 2]);
            $table->enum('bio_medical', [0, 1, 2]);
            $table->enum('inventory', [0, 1, 2]);
            $table->enum('forms', [0, 1, 2]);
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('corporate_admin_user_id')->references('corporate_admin_user_id')->on('corporate_admin_user')->onDelete('cascade');
            $table->foreign('location_id')->references('location_id')->on('master_corporate')->onDelete('cascade');
            $table->foreign('qualification_id')->references('qualification_id')->on('doctor_qualification')->onDelete('cascade');
            // $table->foreign('pharmacy_id')->references('pharmacy_id')->on('ohc_pharmacy')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the tables in reverse order to avoid foreign key issues
        Schema::dropIfExists('corporate_menu_rights_doctor');
        Schema::dropIfExists('corporate_menu_rights');
    }
  
};

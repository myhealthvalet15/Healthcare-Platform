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
        Schema::create('prescription', function (Blueprint $table) {
            $table->integer('prescription_row_id')->primary();
            $table->string('user_id')->nullable();
            $table->integer('op_registry_id');
            $table->integer('corporate_ohc_id');
            $table->string('prescription_id')->nullable();
            $table->string('master_doctor_id')->nullable();
            $table->string('role_id')->nullable();
            $table->integer('template_id')->nullable();
            $table->string('master_hcsp_user_id')->nullable();
            $table->string('attachment_id')->nullable();
            $table->string('is_conformance')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->text('user_notes')->nullable();
            $table->string('share_with_patient')->nullable();
            $table->string('case_id')->nullable();
            $table->string('draft_save')->nullable();
            $table->integer('fav_pharmacy')->nullable();
            $table->integer('fav_lab')->nullable();
            $table->dateTime('prescription_date')->nullable();
            $table->integer('order_status')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('created_by')->nullable();
            $table->integer('created_role')->nullable();
            $table->dateTime('modified_on')->nullable();
            $table->string('corporate_location_id')->nullable();
            $table->integer('ohc')->nullable();
            $table->integer('alternate_drug')->nullable();
            $table->string('active_status')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription');
    }
};

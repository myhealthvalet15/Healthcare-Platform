<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('healthplan_assigned', function (Blueprint $table) {
            $table->id();
            $table->integer('master_lab_id');
            $table->int('test_code', 100);
            $table->string('user_id');
            $table->integer('lab_healthplan')->nullable();
            $table->string('corporate_location_id');
            $table->integer('corporate_healthplan_id');
            $table->integer('generate_test_request_id');
            $table->enum('visit_status', ['Application', 'Walkin'])->nullable();
            $table->integer('pre_emp_user_id')->nullable();
            $table->timestamp('next_assess_date')->useCurrent();
            $table->timestamp('created_on')->useCurrent();
            $table->integer('created_by')->nullable();
            $table->index('test_code');
            $table->foreign('user_id')->references('user_id')->on('employee_user_mapping')->cascadeOnDelete();
            $table->foreign('corporate_location_id')->references('location_id')->on('master_corporate');
            $table->foreign('corporate_healthplan_id')->references('corporate_healthplan_id')->on('corporate_healthplan')->cascadeOnDelete();
        });


        Schema::create('healthplan_assigned_status', function (Blueprint $table) {
            $table->id();
            $table->int('test_code');
            $table->enum('status', ['Pending', 'Schedule', 'In Process', 'Test Completed', 'Result Ready', 'No Show', 'Certified', 'Cancelled']);
            $table->timestamp('status_date_time');
            $table->timestamp('inserted_on')->useCurrent();
            $table->integer('inserted_by')->nullable();

            $table->foreign('test_code')->references('test_code')->on('healthplan_assigned')->cascadeOnDelete();
        });

        Schema::create('prescribed_test', function (Blueprint $table) {
            $table->id();
            $table->int('test_code');
            $table->boolean('isVp')->default(0);
            $table->boolean('isAssignedHealthplan')->default(0);
            $table->string('prescription_id', 255)->nullable();
            $table->string('case_id', 255)->nullable();
            $table->string('user_id');
            $table->string('doctor_id', 255);
            $table->integer('hosp_id');
            $table->integer('lab_id');
            $table->integer('ohc_id');
            $table->string('corporate_id');
            $table->boolean('fromOp')->default(true);
            $table->integer('preemp_user_id');
            $table->date('test_date');
            $table->date('test_due_date');
            $table->string('test_modified', 255)->nullable();
            $table->integer('favourite_lab')->nullable();
            $table->datetime('created_on');
            $table->string('created_by', 255);
            $table->string('file_name', 255)->nullable();

            $table->foreign('test_code')->references('test_code')->on('healthplan_assigned')->cascadeOnDelete();
            $table->foreign('user_id')->references('user_id')->on('employee_user_mapping')->cascadeOnDelete();
        });

        Schema::create('healthplan_certification', function (Blueprint $table) {
            $table->id();
            $table->integer('healthplan_certification_id');
            $table->string('user_id');
            $table->int('test_code');
            $table->integer('certification_id');
            $table->datetime('certified_on');
            $table->date('next_assessment_date');
            $table->json('condition')->nullable();
            $table->json('color_condition')->nullable();
            $table->string('remarks')->nullable();
            $table->datetime('inserted_on');
            $table->foreign('user_id')->references('user_id')->on('employee_user_mapping')->cascadeOnDelete();
            $table->foreign('test_code')->references('test_code')->on('healthplan_assigned')->cascadeOnDelete();
            $table->foreign('certification_id')->references('certificate_id')->on('certification')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('healthplan_certification');
        Schema::dropIfExists('prescribed_test');
        Schema::dropIfExists('healthplan_assigned_status');
        Schema::dropIfExists('healthplan_assigned');
    }
};

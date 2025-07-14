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
        Schema::create('op_registry', function (Blueprint $table) {
            $table->integer('op_registry_id')->primary();
            $table->integer('follow_up_op_registry_id')->default(0);
            $table->integer('doctor_id')->default(0);
            $table->integer('parent_id')->nullable();
            $table->integer('followup_count')->nullable();
            $table->string('master_user_id');
            $table->string('corporate_id');
            $table->string('location_id');
            $table->integer('ohc_id');
            $table->string('shift', 10)->nullable();
            $table->date('created_date_time');
            $table->enum('type_of_incident', ['outsideAccident', 'industrialAccident', 'medicalIllness'])
            ->default('medicalIllness')
            ->change();
            $table->string('nature_injury')->nullable();
            $table->string('body_part')->nullable();
            $table->string('body_side')->nullable();
            $table->string('mechanism_injury')->nullable();
            $table->string('type_of_injury')->nullable();
            $table->string('site_of_injury')->nullable();
            $table->string('place_of_accident')->nullable();
            $table->string('injury_color_text')->nullable();
            $table->string('first_aid_by')->nullable();
            $table->string('incident_occurance')->nullable();
            $table->string('symptoms')->nullable();
            $table->string('medical_system')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->text('past_medical_history')->nullable();
            $table->date('day_of_registry')->nullable();
            $table->string('attachment')->nullable();
            $table->string('open_status');
            $table->string('fir_status')->nullable();
            $table->string('description')->nullable();
            $table->boolean('movement_slip');
            $table->boolean('fitness_certificate');
            $table->boolean('physiotherapy');
            $table->timestamps();
        });

        Schema::create('op_outside_referral', function (Blueprint $table) {
            $table->id('op_outside_referral_id');
            $table->integer('op_registry_id');
            $table->foreign('op_registry_id')->references('op_registry_id')->on('op_registry')->onDelete('cascade');
            $table->string('hospital_name');
            $table->string('vehicle_type')->nullable();
            $table->enum('vehicle_type', ['ambulance', 'own'])->change();
            $table->string('vehicle_number')->nullable();
            $table->string('ambulance_driver')->nullable();
            $table->string('ambulance_number')->nullable();
            $table->string('accompanied_by')->nullable();
            $table->timestamp('ambulance_outtime')->nullable();
            $table->timestamp('ambulance_intime')->nullable();
            $table->integer('meter_out')->nullable();
            $table->integer('meter_in')->nullable();
            $table->booloean('employee_esi');
            $table->integer('mr_number')->nullable();
            $table->timestamps();
        });

        Schema::create('op_registry_times', function (Blueprint $table) {
            $table->id('op_registry_times_id');
            $table->unsignedBigInteger('op_registry_id');
            $table->foreign('op_registry_id')->references('op_registry_id')->on('op_registry')->onDelete('cascade');
            $table->timestamp('incident_date_time');
            $table->timestamp('reporting_date_time')->nullable();
            $table->timestamp('leave_from_date_time')->nullable();
            $table->timestamp('leave_upto_date_time')->nullable();
            $table->timestamp('join_date_time')->nullable();
            $table->string('lost_hours')->nullable();
            $table->timestamp('out_date_time')->nullable();
            $table->string('created_by');
            $table->timestamp('created_date_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('op_registry_times');
        Schema::dropIfExists('op_outside_referral');
        Schema::dropIfExists('op_registry');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalizationDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('hospitalization_details', function (Blueprint $table) {
            $table->bigIncrements('hospitalization_details_id');

            $table->unsignedBigInteger('ohc_id')->nullable(); // Occupational Health Center ID
            $table->unsignedBigInteger('master_user_id')->nullable();
            $table->unsignedBigInteger('hospital_id')->nullable();

            $table->string('hospital_name')->nullable();

            $table->dateTime('from_datetime')->nullable();
            $table->dateTime('to_datetime')->nullable();

            $table->text('description')->nullable();

            $table->unsignedBigInteger('condition_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->string('attachment_discharge')->nullable(); // file path or filename

            $table->timestamps(); // adds created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospitalization_details');
    }
}

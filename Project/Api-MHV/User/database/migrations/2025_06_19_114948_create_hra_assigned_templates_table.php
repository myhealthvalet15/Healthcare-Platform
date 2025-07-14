<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHraAssignedTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hra_assigned_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->string('location_id', 25);
            $table->string('corporate_id', 25);
            $table->string('location');
            $table->json('employee_type');
            $table->json('department');
            $table->json('designation')->nullable();
            $table->date('from_date');
            $table->date('to_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hra_assigned_templates');
    }
}

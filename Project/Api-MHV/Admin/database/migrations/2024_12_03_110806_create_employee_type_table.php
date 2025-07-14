<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_type', function (Blueprint $table) {
            $table->bigIncrements('employee_type_id'); // Primary key
            $table->string('corporate_id', 255); // Foreign key column
            $table->string('employee_type_name', 255);
            $table->tinyInteger('active_status')->default(0); // Default value is 0
            $table->timestamps(); // Includes created_at and updated_at columns

            // Foreign key constraint
            $table->foreign('corporate_id')
                ->references('corporate_id')
                ->on('master_corporate')
                ->onDelete('cascade') // Cascade delete
                ->onUpdate('cascade'); // Cascade update
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_type');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorporateHealthplanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_healthplan', function (Blueprint $table) {
            $table->increments('corporate_healthplan_id'); // Auto-incrementing primary key
            $table->string('corporate_id');
            $table->foreign('corporate_id')
                ->references('corporate_id')
                ->on('master_corporate')
                ->onDelete('cascade');
            $table->string('healthplan_title');
            $table->string('healthplan_description')->nullable();
            $table->json('master_test_id');
            $table->json('certificate_id');
            $table->boolean('isPreEmployement');
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('modified_date')->useCurrentOnUpdate()->nullable();
            $table->json('forms')->nullable();
            $table->json('gender');
            $table->boolean('active_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_healthplan');
    }
}

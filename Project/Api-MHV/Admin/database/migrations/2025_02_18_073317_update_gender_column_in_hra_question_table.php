<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGenderColumnInHraQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hra_question', function (Blueprint $table) {
            $table->json('gender')->change(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hra_question', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'third gender'])->change(); 
        });
    }
}

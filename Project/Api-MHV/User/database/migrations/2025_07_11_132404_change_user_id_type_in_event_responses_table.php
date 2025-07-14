<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserIdTypeInEventResponsesTable extends Migration
{
    public function up()
    {
        // Step 1: Drop foreign key constraint (if it exists)
        Schema::table('event_responses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Step 2: Change column type to VARCHAR(255)
        Schema::table('event_responses', function (Blueprint $table) {
            $table->string('user_id', 255)->change();
        });

        // Step 3: Re-add foreign key if the referenced column is also VARCHAR(255)
        Schema::table('event_responses', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Step 1: Drop foreign key constraint
        Schema::table('event_responses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Step 2: Revert column type back to INTEGER (adjust as needed)
        Schema::table('event_responses', function (Blueprint $table) {
            $table->integer('user_id')->change();
        });

        // Step 3: Re-add the original foreign key
        Schema::table('event_responses', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}

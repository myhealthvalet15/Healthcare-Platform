<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeUserIdTypeInEventResponsesTable extends Migration
{
    public function up()
    {
        // Step 1: Drop the foreign key safely
        try {
            Schema::table('event_responses', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Exception $e) {
            logger()->warning('Foreign key event_responses_user_id_foreign not found or already dropped.');
        }

        // Step 2: Change column type to string
        Schema::table('event_responses', function (Blueprint $table) {
            $table->string('user_id', 255)->change();
        });

        // Step 3: Re-add foreign key (ensure 'users.id' is string too)
        try {
            Schema::table('event_responses', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            logger()->warning('Foreign key could not be added. Check if users.id is of type string.');
        }
    }

    public function down()
    {
        // Drop foreign key
        try {
            Schema::table('event_responses', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Exception $e) {
            logger()->warning('Foreign key event_responses_user_id_foreign not found during rollback.');
        }

        // Revert user_id column to integer
        Schema::table('event_responses', function (Blueprint $table) {
            $table->integer('user_id')->change();
        });

        // Re-add original foreign key (assuming users.id is integer)
        try {
            Schema::table('event_responses', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            logger()->warning('Could not re-add original foreign key in rollback.');
        }
    }
}

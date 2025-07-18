<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ChangeUserIdTypeInEventResponsesTable extends Migration
{
    public function up()
    {
        // Check first: only proceed if users.id is also a string
        // Otherwise, this whole migration will break relational integrity

        // Step 1: Drop foreign key if it exists
        try {
            Schema::table('event_responses', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Exception $e) {
            Log::warning('Foreign key event_responses_user_id_foreign not found or already dropped.');
        }

        // Step 2: Change user_id to string
        Schema::table('event_responses', function (Blueprint $table) {
            $table->string('user_id', 255)->change();
        });

        // Step 3: Re-add foreign key constraint
        try {
            Schema::table('event_responses', function (Blueprint $table) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            });
        } catch (\Exception $e) {
            Log::warning('Could not re-add foreign key. Check that users.id is of type string.');
        }
    }

    public function down()
    {
        // Step 1: Drop the string FK
        try {
            Schema::table('event_responses', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Exception $e) {
            Log::warning('Foreign key not found during rollback.');
        }

        // Step 2: Change column back to integer
        Schema::table('event_responses', function (Blueprint $table) {
            $table->integer('user_id')->change();
        });

        // Step 3: Re-add original FK assuming users.id is integer
        try {
            Schema::table('event_responses', function (Blueprint $table) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            });
        } catch (\Exception $e) {
            Log::warning('Could not re-add original foreign key in rollback.');
        }
    }
}

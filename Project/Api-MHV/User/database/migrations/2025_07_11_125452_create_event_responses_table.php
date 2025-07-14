<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('event_responses', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('user_id');

            // Corporate ID as string
            $table->string('corporate_id', 255);

            // Status (yes/no or 1/0)
            $table->enum('status', ['yes', 'no']);

            $table->timestamps();

            // Foreign key constraints (optional but recommended)
            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_responses');
    }
}

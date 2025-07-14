<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id'); // Primary Key
            $table->string('corporate_id');
            $table->string('event_name');
            $table->text('event_description')->nullable();
            $table->string('guest_name');
            $table->dateTime('from_datetime');
            $table->dateTime('to_datetime');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
}

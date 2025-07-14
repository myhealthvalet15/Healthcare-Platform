<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventDetailsTable extends Migration
{
    public function up(): void
    {
        Schema::create('event_details', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('corporate_id');
            $table->unsignedBigInteger('event_row_id');
            $table->integer('employee_type');
            $table->integer('department');
            $table->string('condition')->nullable();
            $table->integer('test_taken');
            $table->timestamps();

            $table->foreign('event_row_id')->references('event_id')->on('events')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_details');
    }
}

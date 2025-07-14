<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEventDetailsStoreJsonFields extends Migration
{
    public function up()
    {
        Schema::table('event_details', function (Blueprint $table) {
            $table->json('department')->change();
            $table->json('employee_type')->change();
            $table->json('test_taken')->change();
        });
    }

    public function down()
    {
        Schema::table('event_details', function (Blueprint $table) {
            $table->integer('department')->change();
            $table->integer('employee_type')->change();
            $table->integer('test_taken')->change();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('test_group', function (Blueprint $table) {
            $table->unsignedInteger('test_group_id')->autoIncrement();
            $table->string('test_group_name');
            $table->integer('group_type');
            $table->unsignedInteger('group_id')->nullable();
            $table->unsignedInteger('subgroup_id')->nullable();
            $table->boolean('active_status');
            $table->timestamps();

            $table->foreign('group_id')->references('test_group_id')->on('test_group')->onDelete('cascade');
            $table->foreign('subgroup_id')->references('test_group_id')->on('test_group')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_group');
    }
};

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
        Schema::create('mhv_admin', function (Blueprint $table) {
            $table->bigIncrements('mhv_admin_id');
            $table->string('admin_name', 255);
            $table->string('email', 255);
            $table->string('password', 255);
            $table->tinyInteger('active_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mhv_admin');
    }
};

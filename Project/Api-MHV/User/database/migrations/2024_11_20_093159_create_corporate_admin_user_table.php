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
        Schema::create('corporate_admin_user', function (Blueprint $table) {
            $table->id();
            $table->string('corporate_admin_user_id')->unique();
            $table->string('corporate_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->string('gender');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile_country_code');
            $table->string('mobile_num');
            $table->timestamp('created_on')->useCurrent();
            $table->integer('createdby');
            $table->boolean('password_changed')->default(0);
            $table->boolean('super_admin')->default(0);
            $table->string('signup_by');
            $table->timestamp('signup_on')->useCurrent();
            $table->string('aadhar');
            $table->integer('age');
            $table->boolean('active_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_admin_user');
    }
};

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
        Schema::table('corporate_admin_user', function (Blueprint $table) {
        $table->string('department')->nullable()->before('email');
        $table->string('setting')->nullable()->before('email');
        $table->dropColumn(['dob']);
        $table->dropColumn(['age']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corporate_admin_user', function (Blueprint $table) {
        // Reverse the changes if necessary
        $table->dropColumn(['department']);
        $table->dropColumn(['setting']);
    });
    }
};

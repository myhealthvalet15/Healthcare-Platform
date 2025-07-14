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
        Schema::table('corporate_menu_rights', function (Blueprint $table) {
        $table->string('corporate_user_id')->nullable(false)->before('corporate_admin_user_id');
       
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corporate_menu_rights', function (Blueprint $table) {
        // Reverse the changes if necessary
        $table->dropColumn(['corporate_user_id']);
        
    });
    }
};

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
        Schema::table('ohc_menu_rights', function (Blueprint $table) {
            $table->string('corporate_user_id', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ohc_menu_rights', function (Blueprint $table) {
            $table->dropColumn('corporate_user_id');
        });
    }
};

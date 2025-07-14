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
            $table->string('email_hash')->nullable()->index()->before('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corporate_admin_user', function (Blueprint $table) {
            $table->dropColumn('email_hash');
        });
    }
};

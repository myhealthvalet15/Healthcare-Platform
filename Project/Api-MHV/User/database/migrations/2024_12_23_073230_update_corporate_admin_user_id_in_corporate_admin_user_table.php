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
            // Ensure the column matches the foreign key's requirements
            $table->string('corporate_admin_user_id', 255)
                  ->collation('utf8mb4_unicode_ci')
                  ->change();

            // Add an index if not already present
            $table->index('corporate_admin_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corporate_admin_user', function (Blueprint $table) {
            // Reverse the changes if necessary
            $table->dropIndex(['corporate_admin_user_id']);
        });
    }
};

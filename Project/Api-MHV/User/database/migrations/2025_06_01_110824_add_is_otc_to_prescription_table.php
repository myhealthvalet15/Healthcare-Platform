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
        
    Schema::table('prescription', function (Blueprint $table) {
        $table->boolean('is_otc')->default(0)->after('op_registry_id'); // adjust the position if needed
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription', function (Blueprint $table) {
        $table->dropColumn('is_otc');
    });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('corporate_healthplan', function (Blueprint $table) {
            $table->json('gender_new')->nullable();
        });

        DB::table('corporate_healthplan')
            ->update([
                'gender_new' => DB::raw("JSON_ARRAY(gender)")
            ]);

        Schema::table('corporate_healthplan', function (Blueprint $table) {
            $table->dropColumn('gender');
        });

        Schema::table('corporate_healthplan', function (Blueprint $table) {
            $table->renameColumn('gender_new', 'gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corporate_healthplan', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'other', 'both'])->nullable();
        });
    }
};

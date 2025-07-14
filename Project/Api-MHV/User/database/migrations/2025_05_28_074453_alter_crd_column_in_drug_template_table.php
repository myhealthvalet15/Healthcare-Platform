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
         Schema::table('drug_template', function (Blueprint $table) {
            $table->boolean('crd')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drug_template', function (Blueprint $table) {
            $table->string('crd')->change(); // Revert back to varchar if necessary
        });
    }
};

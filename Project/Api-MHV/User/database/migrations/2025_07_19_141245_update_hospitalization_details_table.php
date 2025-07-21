<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHospitalizationDetailsTable extends Migration
{
    public function up(): void
    {
        Schema::table('hospitalization_details', function (Blueprint $table) {
            // Step 1: Rename column (only if it exists)
            if (Schema::hasColumn('hospitalization_details', 'ohc_id')) {
                $table->renameColumn('ohc_id', 'op_registry_id');
            }
        });

        // Step 2: Add new columns in a separate Schema::table block
        Schema::table('hospitalization_details', function (Blueprint $table) {
            $table->unsignedBigInteger('doctor_id')->nullable()->after('op_registry_id');
            $table->string('doctor_name')->nullable()->after('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::table('hospitalization_details', function (Blueprint $table) {
            if (Schema::hasColumn('hospitalization_details', 'op_registry_id')) {
                $table->renameColumn('op_registry_id', 'ohc_id');
            }

            $table->dropColumn('doctor_id');
            $table->dropColumn('doctor_name');
        });
    }
}

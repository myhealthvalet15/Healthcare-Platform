<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCalibrationHistoryColumnInCorporateInventoryTable extends Migration
{
    public function up()
    {
        // Modify the 'calibration_history' column to be json if it's not already
        Schema::table('corporate_inventory', function (Blueprint $table) {
            // Ensure the 'calibration_history' column is JSON and nullable
            $table->json('calibration_history')->nullable()->change();
        });
    }

    public function down()
    {
        // This will revert the 'calibration_history' column back to a TEXT type if needed
        Schema::table('corporate_inventory', function (Blueprint $table) {
            $table->text('calibration_history')->nullable()->change();
        });
    }
}

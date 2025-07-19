<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachmentTestReportsAndOtherConditionNameToHospitalizationDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('hospitalization_details', function (Blueprint $table) {
            $table->string('attachment_test_reports')->nullable()->after('attachment_discharge');
            $table->string('other_condition_name')->nullable()->after('condition_id');
        });
    }

    public function down()
    {
        Schema::table('hospitalization_details', function (Blueprint $table) {
            $table->dropColumn('attachment_test_reports');
            $table->dropColumn('other_condition_name');
        });
    }
}

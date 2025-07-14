<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        Schema::table('master_user', function (Blueprint $table) {
            $table->string('mobile_hash', 255)->nullable()->after('mob_num');
            $table->string('email_hash', 255)->nullable()->after('email');  
            $table->string('aadhar_hash', 255)->nullable()->after('aadhar_id'); 
            $table->string('abha_hash', 255)->nullable()->after('abha_id'); 
            $table->string('first_name', 255)->nullable(false)->change();
            $table->string('last_name', 255)->nullable(false)->change();
            $table->string('first_name_hash', 255)->nullable(false)->change();
            $table->string('last_name_hash', 255)->nullable(false)->change();
            $table->string('mob_country_code', 255)->nullable(false)->change();
            $table->bigInteger('mob_num')->nullable(false)->change();
            $table->string('dob');
            $table->string('gender');
        });
        Schema::table('master_user', function (Blueprint $table) {
            $table->index('email_hash');
            $table->index('aadhar_hash');
            $table->index('abha_hash');
            $table->index('mobile_hash');
            $table->index('first_name_hash');
            $table->index('last_name_hash');
        });
        Schema::table('employee_user_mapping', function (Blueprint $table) {
            $table->string('employee_id')->nullable(false)->change();
            $table->string('designation')->nullable(false)->change();
            $table->date('from_date')->nullable(false)->change();
        });
    }
    public function down()
    {
        Schema::table('master_user', function (Blueprint $table) {
            $table->dropColumn('mobile_hash');
            $table->dropColumn('email_hash');
            $table->dropColumn('first_name_hash');
            $table->dropColumn('last_name_hash');
            $table->dropColumn('aadhar_hash');
            $table->dropColumn('abha_hash');
            $table->string('first_name', 255)->nullable()->change();
            $table->string('last_name', 255)->nullable()->change();
            $table->string('mob_country_code', 255)->nullable()->change();
            $table->bigInteger('mob_num')->nullable()->change();
            $table->string('dob')->nullable()->change();
        });
        Schema::table('employee_user_mapping', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->string('employee_id')->nullable()->change();
            $table->string('designation')->nullable()->change();
            $table->date('from_date')->nullable()->change();
        });
    }
};

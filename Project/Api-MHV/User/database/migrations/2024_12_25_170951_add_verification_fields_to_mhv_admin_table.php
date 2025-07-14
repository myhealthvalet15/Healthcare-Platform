<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationFieldsToMhvadminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mhv_admin', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('verification_code')->nullable();
            $table->boolean('verified')->default(0);
            $table->timestamp('verification_expires_at')->nullable();
            $table->integer('verification_attempts')->default(0);
            $table->timestamp('verification_attempts_locked_until')->nullable();
            $table->integer('verification_resend_attempts')->default(0);
            $table->timestamp('verification_resend_attempts_locked_until')->nullable();
            $table->string('verification_resend_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mhv_admin', function (Blueprint $table) {
            $table->dropColumn('two_factor_enabled');
            $table->dropColumn('verification_code');
            $table->dropColumn('verification_expires_at');
            $table->dropColumn('verified');
            $table->dropColumn('verification_resend_attempts');
            $table->dropColumn('verification_resend_attempts_locked_until');
            $table->dropColumn('verification_resend_token');
        });
    }
}

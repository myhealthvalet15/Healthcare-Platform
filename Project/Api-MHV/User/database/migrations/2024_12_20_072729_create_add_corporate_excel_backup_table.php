<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_corporate_excel_backup', function (Blueprint $table) {
            $table->id(); 
            $table->string('user_id');
            $table->string('file_name');
            $table->longText('file_base64');
            $table->enum('status', ['accepted', 'denied', 'partial'])->default('accepted');
            $table->longText('denied_reason')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('add_corporate_excel_backup');
    }
};

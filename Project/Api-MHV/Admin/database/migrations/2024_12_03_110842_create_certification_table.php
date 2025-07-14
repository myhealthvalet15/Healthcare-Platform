<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certification', function (Blueprint $table) {
            $table->integer('certificate_id')->primary();
            $table->string('corporate_id');
            $table->string('certification_title');
            $table->string('short_tag');
            $table->text('content');
            $table->json('condition');
            $table->json('color_condition');
            $table->boolean('active_status')->default(0);
            $table->timestamps();
            $table->foreign('corporate_id')
                    ->references('corporate_id')
                    ->on('master_corporate')
                    ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certification');
    }
};

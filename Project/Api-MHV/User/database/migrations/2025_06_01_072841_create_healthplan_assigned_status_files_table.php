<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthplanAssignedStatusFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('healthplan_assigned_status_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('healthplan_assigned_status_id');
            $table->string('file_name');
            $table->string('file_type', 100);
            $table->longText('file_base64');
            $table->dateTime('uploaded_at')->useCurrent();

            $table->foreign('healthplan_assigned_status_id', 'fk_status_file_to_status')
                ->references('id')
                ->on('healthplan_assigned_status')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('healthplan_assigned_status_files');
    }
}

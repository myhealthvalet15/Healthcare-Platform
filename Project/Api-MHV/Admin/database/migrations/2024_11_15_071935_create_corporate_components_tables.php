<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('corporate_component_modules', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id')->unique();
            $table->string('module_name', 255)->unique();
            $table->timestamps();
        });

        Schema::create('corporate_component_submodules', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id');
            $table->integer('sub_module_id')->unique();
            $table->string('sub_module_name', 255)->unique();
            $table->timestamps();

            $table->foreign('module_id')
                ->references('module_id')
                ->on('corporate_component_modules')
                ->onDelete('cascade');
        });

        Schema::create('corporate_components', function (Blueprint $table) {
            $table->id();
            $table->string('corporate_id');
            $table->integer('module_id');
            $table->json('sub_module_id')->nullable();
            $table->json('hra_templates')->nullable();
            $table->timestamps();

            $table->foreign('module_id')
                ->references('module_id')
                ->on('corporate_component_modules');
            // $table->foreign('sub_module_id')
            //     ->references('sub_module_id')
            //     ->on('corporate_component_submodules');
        });
    }

    public function down()
    {
        Schema::dropIfExists('corporate_components');
        Schema::dropIfExists('corporate_component_submodules');
        Schema::dropIfExists('corporate_component_modules');
    }
};

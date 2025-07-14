<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('master_test', function (Blueprint $table) {
            $table->increments('master_test_id');
            $table->string('test_name')->unique();
            $table->string('test_desc')->nullable();
            $table->unsignedInteger('testgroup_id')->nullable();
            $table->unsignedInteger('subgroup_id')->nullable();
            $table->unsignedInteger('subsubgroup_id')->nullable();
            $table->string('unit')->nullable();
            $table->json('age_range')->nullable();
            $table->json('m_min_max')->nullable();
            $table->json('f_min_max')->nullable();
            $table->unsignedInteger('type')->nullable();
            $table->string('numeric_type')->nullable();
            $table->string('multiple_text_value_description')->nullable();
            $table->string('condition')->nullable();
            $table->string('numeric_condition')->nullable();
            $table->text('normal_values')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_on')->useCurrent();
        });
        Schema::create('hra_question', function (Blueprint $table) {
            $table->unsignedInteger('question_id')->autoIncrement();
            $table->string('question', 256);
            $table->enum('types', ['Select Box', 'Input Box', 'Check Box', 'Radio Button']);
            $table->json('answer')->nullable();
            $table->json('trigger_wer')->nullable();
            $table->json('points');
            $table->boolean('active_status')->default(1);
            $table->string('image')->nullable();
            // $table->integer('input_box')->default(0);
            $table->string('formula')->nullable();
            $table->json('test_id')->nullable();
            $table->text('comments')->nullable();
            $table->string('dashboard_title', 150)->nullable();
            $table->json('comp_value')->nullable();
            $table->enum('gender', ['male', 'female', 'third gender']);
            $table->timestamps();
        });
        Schema::create('hra_factors', function (Blueprint $table) {
            $table->increments('factor_id');
            $table->string('factor_name');
            $table->integer('active_status')->default(1);
            $table->integer('priority')->nullable()->unique();
            $table->timestamps();
        });
        Schema::create('hra_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->string('template_name');
            $table->integer('total_adjustment_value')->nullable();
            $table->integer('factor_id')->nullable();
            $table->integer('maximum_value')->nullable();
            $table->integer('factor_adjustment_value')->nullable();
            $table->string('health_index_formula')->nullable();
            $table->integer('priority')->nullable();
            $table->boolean('published')->default(0);
            $table->integer('active_status')->default(1);
            $table->timestamps();
            $table->index('factor_id', 'hra_templates_fid_idx');
            $table->index('template_id', 'hra_templates_tid_idx');
        });
        Schema::create('hra_template_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('template_id');
            $table->unsignedInteger('factor_id');
            $table->integer('factor_priority')->nullable();
            $table->unsignedInteger('question_id');
            $table->integer('question_priority')->nullable();
            $table->integer('type');
            $table->json('trigger_1')->nullable();
            $table->json('trigger_2')->nullable();
            $table->json('trigger_3')->nullable();
            $table->json('trigger_4')->nullable();
            $table->json('trigger_5')->nullable();
            $table->json('trigger_6')->nullable();
            $table->json('trigger_7')->nullable();
            $table->json('trigger_8')->nullable();
            $table->boolean('status')->default(0);
            $table->json('high_data')->nullable();
            $table->timestamps();
        });
        Schema::create('prescribed_test_data', function (Blueprint $table) {
            $table->increments('id');
            $table->int('test_code');
            $table->string('test_id', 255);
            $table->string('test_results', 255)->unique();
            $table->string('text_condition', 255);
            $table->boolean('fromOp')->default(true);
            $table->timestamps();
        });
        Schema::create('master_user', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 12);
            $table->string('user_type', 255)->nullable();
            $table->string('aadhar_id', 255)->nullable();
            $table->string('area', 255)->nullable();
            $table->string('zipcode', 255)->nullable();
            $table->string('password', 255);
            $table->boolean('password_changed')->default(false);
            $table->string('mob_country_code', 255)->nullable();
            $table->string('mob_num', 255)->nullable();
            $table->string('security_code', 255)->nullable();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('gender', 255)->nullable();
            $table->date('dob')->nullable();
            $table->string('email', 255)->unique();
            $table->string('alternative_email', 255)->nullable();
            $table->timestamp('valid_upto')->nullable();
            $table->boolean('email_confirm_status')->default(false);
            $table->timestamps();
            $table->index('user_id');
            $table->boolean('isactive')->default(true);
        });
        Schema::create('master_user_details', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 12);
            $table->unsignedInteger('salutation_doctype_static_id')->nullable();
            $table->string('prof_image', 255)->nullable();
            $table->text('secret_ques_secret_ans')->nullable();
            $table->boolean('isactive')->default(true);
            $table->boolean('isuser')->default(false);
            $table->boolean('is_doctor')->default(false);
            $table->unsignedInteger('doctorid')->nullable();
            $table->unsignedInteger('user_no_of_dep')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamp('registration_date')->nullable();
            $table->string('pay_status', 255)->nullable();
            $table->unsignedInteger('insertedby')->nullable();
            $table->string('insertedrole', 255)->nullable();
            $table->timestamps();
            $table->unsignedInteger('modified_by')->nullable();
            $table->string('modified_role', 255)->nullable();
            $table->timestamp('modified_on')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->foreign('user_id')->references('user_id')->on('master_user')->onDelete('cascade');
        });
        Schema::create('hra_induvidual_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('template_id');
            $table->string('user_id', 12);
            $table->unsignedInteger('question_id');
            $table->unsignedInteger('trigger_question_of')->nullable();
            $table->string('answer');
            $table->integer('points')->nullable();
            $table->integer('test_results')->nullable();
            $table->integer('question_status');
            $table->integer('reference_question');
            $table->timestamps();
        });
        Schema::create('hra_overall_result', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 12);
            $table->unsignedInteger('corporate_template_id');
            $table->unsignedInteger('hra_template_id');
            $table->string('corporate_id');
            $table->string('location_id');
            $table->integer('hl1');
            $table->string('designation', 155);
            $table->string('obtained_points', 100);
            $table->string('actual_points', 10);
            $table->string('health_index', 10);
            $table->integer('factor_score');
            $table->dateTime('completed_date');
            $table->string('result_text', 100);
            $table->date('next_assessment_date__')->nullable();
            $table->date('certified_date__')->nullable();
            $table->string('certified_status__', 50)->nullable();
            $table->string('certified_color__', 50)->nullable();
            $table->text('certified_description__')->nullable();
            $table->timestamps();
        });
        Schema::create('corporate_template_assign', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('template_id');
            $table->string('corporate_id');
            $table->string('location_id');
            $table->string('hl1_id')->nullable();
            $table->string('designation')->nullable();
            $table->integer('employee_type');
            $table->string('assigned_employees', 12);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('active_status');
            $table->string('certificate')->nullable();
            $table->integer('master_doctor_id')->nullable();
            $table->timestamps();
        });
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 12);
            $table->string('insurance_id_insurance_provider', 255)->nullable();
            $table->string('contact_name_contact_number', 255)->nullable();
            $table->string('relationship', 255)->nullable();
            $table->unsignedInteger('upload_generate_id')->nullable();
            $table->unsignedInteger('referredby')->nullable();
            $table->string('referencename', 255)->nullable();
            $table->unsignedInteger('dependent_of')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('master_user')->onDelete('cascade');
        });
        Schema::create('signup', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 12);
            $table->string('signup_type', 255)->nullable();
            $table->string('signup_role', 255)->nullable();
            $table->timestamp('signup_date')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('master_user')->onDelete('cascade');
        });
        Schema::create('health_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 12);
            $table->string('blood_group_with_rh_factor', 255)->nullable();
            $table->string('height', 255)->nullable();
            $table->string('weight', 255)->nullable();
            $table->string('health_color', 255)->nullable();
            $table->text('dashboard_parameters')->nullable();
            $table->text('descriptive_mark')->nullable();
            $table->json('allergic_food')->nullable();
            $table->json('allergic_ingredients')->nullable();
            $table->json('published_conditions')->nullable();
            $table->json('unpublished_conditions')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('master_user')->onDelete('cascade');
        });
        Schema::create('employee_user_mapping', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            $table->string('user_id')->collation('utf8mb4_unicode_ci')->index();
            $table->string('corporate_id')->collation('utf8mb4_unicode_ci')->index();
            $table->string('location_id')->collation('utf8mb4_unicode_ci')->index();
            $table->string('employee_id')->collation('utf8mb4_unicode_ci');
            $table->bigInteger('employee_type_id')->unsigned()->index();
            $table->string('designation')->collation('utf8mb4_unicode_ci');
            $table->bigInteger('hl1_id')->unsigned()->index();
            $table->bigInteger('hl2_id')->unsigned()->index();
            $table->bigInteger('hl3_id')->unsigned()->index()->nullable();
            $table->Integer('corporate_contractors_id')->index()->nullable();
            $table->string('contract_worker_id')->collation('utf8mb4_unicode_ci');
            $table->string('other_id')->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('created_by')->collation('utf8mb4_unicode_ci')->index()->nullable();
            $table->timestamp('created_on')->useCurrent();
            $table->string('modified_by')->nullable();
            $table->datetime('modified_on')->nullable();
            $table->integer('upload_generate_id')->unsigned()->nullable();
            $table->date('from_date');
            $table->date('to_date')->nullable();
            $table->integer('active_status')->default(1);
            $table->foreign('user_id')->references('user_id')->on('master_user')->onDelete('cascade');
            $table->foreign('corporate_id')->references('corporate_id')->on('master_corporate')->onDelete('cascade');
            $table->foreign('hl1_id')->references('hl1_id')->on('corporate_hl1')->onDelete('cascade');
            $table->foreign('hl2_id')->references('hl2_id')->on('corporate_hl2')->onDelete('cascade');
            $table->foreign('hl3_id')->references('hl3_id')->on('corporate_hl3')->onDelete('cascade');
            $table->foreign('employee_type_id')->references('employee_type_id')->on('employee_type')->onDelete('cascade');
            $table->foreign('corporate_contractors_id')->references('corporate_contractors_id')->on('corporate_contractors')->onDelete('cascade');
        });
        Schema::table('hra_template_questions', function (Blueprint $table) {
            $table->index('template_id', 'htq_tid_idx');
            $table->index('question_id', 'htq_qid_idx');
            $table->index('factor_id', 'htq_fid_idx');
        });
        Schema::table('hra_induvidual_answers', function (Blueprint $table) {
            $table->index('template_id', 'hia_tid_idx');
            $table->index('question_id', 'hia_qid_idx');
            $table->index('test_results', 'hia_tr_idx');
            $table->index('user_id', 'hia_uid_idx');
        });
        Schema::table('hra_overall_result', function (Blueprint $table) {
            $table->index('user_id', 'hor_uid_idx');
            $table->index('hra_template_id', 'hor_htid_idx');
        });
        Schema::table('corporate_template_assign', function (Blueprint $table) {
            $table->index('template_id', 'cta_tid_idx');
            $table->index('assigned_employees', 'cta_ae_idx');
        });
        Schema::table('hra_template_questions', function (Blueprint $table) {
            $table->foreign('template_id', 'htq_tid_fk')->references('template_id')->on('hra_templates');
            $table->foreign('question_id', 'htq_qid_fk')->references('question_id')->on('hra_question');
            $table->foreign('factor_id', 'htq_fid_fk')->references('factor_id')->on('hra_factors');
        });
        Schema::table('hra_induvidual_answers', function (Blueprint $table) {
            $table->foreign('template_id', 'hia_tid_fk')->references('template_id')->on('hra_templates');
            $table->foreign('user_id', 'hia_uid_fk')->references('user_id')->on('master_user');
            $table->foreign('test_results', 'hia_tr_fk')->references('test_results')->on('prescribed_tests_datas');
        });
        Schema::table('hra_overall_result', function (Blueprint $table) {
            $table->foreign('hra_template_id', 'hor_htid_fk')->references('template_id')->on('hra_templates');
            $table->foreign('user_id', 'hor_uid_fk')->references('user_id')->on('master_user');
        });
        Schema::table('corporate_template_assign', function (Blueprint $table) {
            $table->foreign('template_id', 'cta_tid_fk')->references('template_id')->on('hra_templates');
            $table->foreign('assigned_employees', 'cta_ae_fk')->references('user_id')->on('master_user_details');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('corporate_template_assign');
        Schema::dropIfExists('hra_overall_result');
        Schema::dropIfExists('hra_induvidual_answers');
        Schema::dropIfExists('master_user_details');
        Schema::dropIfExists('master_user');
        Schema::dropIfExists('prescribed_tests_datas');
        Schema::dropIfExists('hra_template_questions');
        Schema::dropIfExists('hra_templates');
        Schema::dropIfExists('hra_factors');
        Schema::dropIfExists('hra_question');
        Schema::dropIfExists('health_parameters');
        Schema::dropIfExists('signup');
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('master_user_details');
        Schema::dropIfExists('master_user');
        Schema::dropIfExists('employee_user_mapping');
    }
};

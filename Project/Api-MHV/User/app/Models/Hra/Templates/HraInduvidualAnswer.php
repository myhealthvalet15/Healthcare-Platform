<?php

namespace App\Models\Hra\Templates;

use Illuminate\Database\Eloquent\Model;

class HraInduvidualAnswer extends Model
{
    protected $table = 'hra_induvidual_answers';

    protected $fillable = [
        'template_id',
        'user_id',
        'factor_id',
        'question_id',
        'trigger_question_of',
        'answer',
        'points',
        'test_results',
        'question_status',
        'reference_question',
    ];
    protected $casts = [
        'template_id' => 'integer',
        'question_id' => 'integer',
        'user_id' => 'integer',
        'factor_id' => 'integer',
        'trigger_question_of' => 'integer',
        'points' => 'integer',
        'test_results' => 'integer',
        'question_status' => 'integer',
        'reference_question' => 'integer',
    ];
}

<?php

namespace App\Models\Hra\Templates;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HraTemplateQuestions extends Model
{
    use HasFactory;
    protected $fillable = [
            'template_id',
            'factor_id',
            'factor_priority',
            'question_id',
            'question_priority',
            'trigger_1',
            'trigger_2',
            'trigger_3',
            'trigger_4',
            'trigger_5',
            'trigger_6',
            'trigger_7',
            'trigger_8',
            'status',
            'high_data',
            'type',
            'status',
    ];
}

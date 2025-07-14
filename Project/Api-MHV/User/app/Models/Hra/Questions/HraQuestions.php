<?php

namespace App\Models\Hra\Questions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HraQuestions extends Model
{
    use HasFactory;

    protected $table = 'hra_question';

    protected $primaryKey = 'question_id'; // Primary key

    // Define fillable attributes to allow mass assignment
    protected $fillable = [
        'question',
        'types',
        'answer',
        'trigger_wer',
        'points',
        'active_status',
        'image',
        'input_box',
        'formula',
        'test_id',
        'comments',
        'dashboard_title',
        'comp_value',
        'gender',
    ];

    // Ensure that the question is unique and trim extra spaces
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->question = trim($model->question);
        });
    }
}

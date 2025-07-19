<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HraOverallResult extends Model
{
    use HasFactory;

    protected $table = 'hra_overall_result';

    protected $fillable = [
        'user_id',
        'corporate_template_id',
        'hra_template_id',
        'corporate_id',
        'location_id',
        'hl1',
        'designation',
        'obtained_points',
        'actual_points',
        'health_index',
        'factor_score',
        'completed_date',
        'result_text',
    ];

    protected $casts = [
        'completed_date' => 'datetime',
    ];
}

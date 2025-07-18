<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Model;

class HraAssignedTemplate extends Model
{
    protected $table = 'hra_assigned_templates';

    protected $fillable = [
        'template_id',
        'corporate_id',
        'location', 
        'location_id',       
        'employee_type',
        'department',
        'designation',
        'from_date',
        'to_date',
        'next_assessment_date'
    ];

    protected $casts = [
        'employee_type' => 'array',
        'department' => 'array',
        'designation' => 'array',
        'from_date' => 'date',
        'to_date' => 'date',
    ];
}

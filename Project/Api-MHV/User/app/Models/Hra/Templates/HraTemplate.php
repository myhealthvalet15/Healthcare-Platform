<?php

namespace App\Models\Hra\Templates;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HraTemplate extends Model
{
    use HasFactory;

    protected $table = 'hra_templates';
    protected $primaryKey = 'template_id';

    protected $fillable = [
        'id',
        'template_id',
        'template_name',
        'total_adjustment_value',
        'factor_id',
        'maximum_value',
        'factor_adjustment_value',
        'health_index_formula',
        'priority',
        'active_status',
    ];

    protected $casts = [
        'factor_id' => 'array', // Automatically cast to an array when retrieved
    ];
}

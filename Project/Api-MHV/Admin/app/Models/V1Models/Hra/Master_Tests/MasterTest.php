<?php

namespace App\Models\V1Models\Hra\Master_Tests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTest extends Model
{
    use HasFactory;

    protected $table = 'master_test';
    protected $primaryKey = 'master_test_id'; // Correct primary key column
    public $timestamps = false; // No timestamps in your table

    protected $fillable = [
        'test_name',
        'test_desc',
        'testgroup_id',
        'subgroup_id',
        'subsubgroup_id',
        'unit',
        'age_range',
        'm_min_max',
        'f_min_max',
        'type',
        'numeric_type',
        'condition',
        'numeric_condition',
        'normal_values',
        'multiple_text_value_description',
        'remarks',
    ];
}

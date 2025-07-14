<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V1Models\Corporate\EmployeeUserMapping;

class PrescribedTestData extends Model
{
    use HasFactory;

    protected $table = 'prescribed_test_data';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'test_code',
        'master_test_id',
        'test_results',
        'text_condition',
        'fromOp',
        'created_at',
        'updated_at'
    ];
}
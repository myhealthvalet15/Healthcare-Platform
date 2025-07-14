<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    use HasFactory;
    protected $table = 'employee_type'; 
    protected $primaryKey = 'employee_type_id'; 

    protected $fillable = [
        'employee_type_name',
        'corporate_id',
        'checked',
        'active_status',
    ];

    public $incrementing = false; 
    protected $keyType = 'string';
}

<?php
namespace App\Models\V1Models\Corporate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUserMapping extends Model
{
    use HasFactory;
    public $timestamps = false; // Disables the default timestamp behavior
    // Table name, assuming it follows Laravel conventions
    protected $table = 'employee_user_mapping';

    protected $fillable = [
        'user_id',
        'employee_id',
        'location_id',
        'hl1_id',
        'employee_type',
        'employee_type_id',
        'other_id',
        'contract_worker_id',
        'corporate_contractors_id',
        'designation',
        'from_date',
        'corporate_id',
        'isactive',
        'created_by',
    ];
}

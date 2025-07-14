<?php

namespace App\Models\Department;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CorporateHl1 extends Model
{
    use HasFactory;


    protected $table = 'corporate_hl1';


    protected $primaryKey = 'hl1_id';


    public $incrementing = true;


    public $timestamps = true;


    protected $fillable = [
        'hl1_id',
        'corporate_id',
        'location_id',
        'corporate_admin_user_id',
        'hl1_name',
        'hl1_code',
        'active_status',
    ];

    public function employeeUserMappings()
    {
        return $this->hasMany(\App\Models\Corporate\EmployeeUserMapping::class, 'hl1_id', 'hl1_id');
    }


}

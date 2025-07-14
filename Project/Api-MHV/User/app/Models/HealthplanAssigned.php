<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V1Models\Corporate\EmployeeUserMapping;

class HealthplanAssigned extends Model
{
    use HasFactory;

    protected $table = 'healthplan_assigned';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'master_lab_id',
        'test_code',
        'user_id',
        'lab_healthplan',
        'corporate_location_id',
        'corporate_healthplan_id',
        'generate_test_request_id',
        'visit_status',
        'pre_emp_user_id',
        'next_assess_date',
        'created_on',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(EmployeeUserMapping::class, 'user_id', 'user_id');
    }

    public function corporateLocation()
    {
        return $this->belongsTo(MasterCorporate::class, 'corporate_location_id', 'location_id');
    }

    public function corporateHealthplan()
    {
        return $this->belongsTo(CorporateHealthplan::class, 'corporate_healthplan_id', 'corporate_healthplan_id');
    }
}

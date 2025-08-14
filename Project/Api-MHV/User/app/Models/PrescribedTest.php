<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V1Models\Corporate\EmployeeUserMapping;

class PrescribedTest extends Model
{
    use HasFactory;

    protected $table = 'prescribed_test';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'test_code',
        'prescription_id',
        'isVp',
        'isAssignedHealthplan',
        'case_id',
        'user_id',
        'doctor_id',
        'fromOp',
        'hosp_id',
        'lab_id',
        'op_registry_id',
        'corporate_id',
        'location_id',
        'preemp_user_id',
        'test_date',
        'test_due_date',
        'test_modified',
        'favourite_lab',
        'created_on',
        'created_by',
        'file_name',
    ];

    public function healthplanAssigned()
    {
        return $this->belongsTo(HealthplanAssigned::class, 'test_code', 'test_code');
    }

    public function user()
    {
        return $this->belongsTo(EmployeeUserMapping::class, 'user_id', 'user_id');
    }
}
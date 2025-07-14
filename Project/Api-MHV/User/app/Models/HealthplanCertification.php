<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V1Models\Corporate\EmployeeUserMapping;

class HealthplanCertification extends Model
{
    use HasFactory;

    protected $table = 'healthplan_certification';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'healthplan_certification_id',
        'user_id',
        'test_code',
        'certification_id',
        'certified_on',
        'next_assessment_date',
        'condition',
        'color_condition',
        'inserted_on',
        'remarks'
    ];

    public function user()
    {
        return $this->belongsTo(EmployeeUserMapping::class, 'user_id', 'user_id');
    }

    public function healthplanAssigned()
    {
        return $this->belongsTo(HealthplanAssigned::class, 'test_code', 'test_code');
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class, 'certification_id', 'certificate_id');
    }
}

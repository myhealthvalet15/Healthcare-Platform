<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalizationDetails extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'hospitalization_details';

    // Primary key
    protected $primaryKey = 'hospitalization_details_id';

    // Auto-increment is enabled by default
    public $incrementing = true;

    // Timestamps
    public $timestamps = true;

    // Fillable fields
    protected $fillable = [
        'op_registry_id',
        'master_user_id',
        'hospital_id',
        'hospital_name',
        'doctor_id',
        'doctor_name',
        'from_datetime',
        'to_datetime',
        'description',
        'condition_id',
        'other_condition_name',
        'role_id',
        'created_by',
        'attachment_discharge',
        'attachment_test_reports',
    ];

    // Casts
    protected $casts = [
        'from_datetime' => 'datetime',
        'to_datetime' => 'datetime',
    ];
}

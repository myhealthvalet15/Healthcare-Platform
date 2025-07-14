<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OpRegistry;

class OpOutsideReferral extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'op_outside_referral';

    // Primary key
    protected $primaryKey = 'op_outside_referral_id';

    // Mass assignable fields
    protected $fillable = [
        'op_registry_id',
        'hospital_name',
        'vehicle_type',
        'vehicle_number',
        'accompanied_by',
        'ambulance_driver',
        'ambulance_number',
        'ambulance_outtime',
        'ambulance_intime',
        'meter_out',
        'meter_in',
        'employee_esi',
        'mr_number'
    ];

    // Cast timestamps
    protected $casts = [
        'ambulance_outtime' => 'datetime',
        'ambulance_intime' => 'datetime',
    ];

    // Relationship with OpRegistry
    public function registry()
    {
        return $this->belongsTo(OpRegistry::class, 'op_registry_id');
    }
}

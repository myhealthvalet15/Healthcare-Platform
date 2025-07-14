<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OpRegistry;

class OpRegistryTimes extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'op_registry_times';

    // Primary key
    protected $primaryKey = 'op_registry_times_id';

    // Mass assignable fields
    protected $fillable = [
        'op_registry_id',
        'join_date_time',
        'incident_date_time',
        'reporting_date_time',
        'leave_from_date_time',
        'leave_upto_date_time',
        'lost_hours',
        'out_date_time',
        'created_by',
        'created_date_time'
    ];

    // Cast timestamps
    protected $casts = [
        'incident_date_time' => 'datetime',
        'reporting_date_time' => 'datetime',
        'leave_from_date_time' => 'datetime',
        'leave_upto_date_time' => 'datetime',
        'join_date_time' => 'datetime',
        'created_date_time' => 'datetime',
        'out_date_time' => 'datetime'
    ];

    // Relationship with OpRegistry
    public function registry()
    {
        return $this->belongsTo(OpRegistry::class, 'op_registry_id');
    }
}

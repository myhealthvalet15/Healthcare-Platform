<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OpOutsideReferral;
use App\Models\OpRegistryTimes;

class OpRegistry extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'op_registry';

    // Primary key
    protected $primaryKey = 'op_registry_id';

    // Disable auto-incrementing (since you're using integer primary keys)
    public $incrementing = false;

    // Mass assignable fields
    protected $fillable = [
        'op_registry_id',
        'follow_up_op_registry_id',
        'doctor_id',
        'parent_id',
        'followup_count',
        'master_user_id',
        'corporate_id',
        'location_id',
        'referral',
        'corporate_ohc_id',
        'shift',
        'created_date_time',
        'type_of_incident',
        'nature_injury',
        'body_part',
        'body_side',
        'mechanism_injury',
        'type_of_injury',
        'site_of_injury',
        'place_of_accident',
        'injury_color_text',
        'first_aid_by',
        'incident_occurance',
        'symptoms',
        'medical_system',
        'diagnosis',
        'doctor_notes',
        'past_medical_history',
        'day_of_registry',
        'attachment',
        'open_status',
        'fir_status',
        'description',
        'movement_slip',
        'fitness_certificate',
        'physiotherapy',
        'registry_type'
    ];

    protected $casts = [
        'created_date_time' => 'datetime',
        'day_of_registry' => 'date',
    ];

    // Relationships
    public function outsideReferral()
    {
        return $this->hasOne(OpOutsideReferral::class, 'op_registry_id');
    }

    public function registryTimes()
    {
        return $this->hasMany(OpRegistryTimes::class, 'op_registry_id');
    }

    // Scope to filter by status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope to filter by date range
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('created_date_time', [$start, $end]);
    }
}

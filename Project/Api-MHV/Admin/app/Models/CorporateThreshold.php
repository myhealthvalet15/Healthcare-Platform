<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateThreshold extends Model
{
    protected $table = 'corporate_thresholds';

    protected $fillable = [
        'location_id', 
        'lab_threshold_count', 
        'lab_threshold_point', 
        'doctor_threshold_count', 
        'doctor_threshold_point', 
        'disease_mapping', 
        'reminder_expiry', 
        'reminder_issue'
    ];

    public function masterCorporate()
    {
        return $this->belongsTo(MasterCorporate::class, 'location_id', 'location_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthplanAssignedStatusFile extends Model
{
    use HasFactory;

    protected $table = 'healthplan_assigned_status_files';

    protected $fillable = [
        'healthplan_assigned_status_id',
        'file_name',
        'file_type',
        'file_base64',
        'uploaded_at',
    ];

    public $timestamps = false; 

    /**
     * Relationship to healthplan_assigned_status
     */
    public function healthplanAssignedStatus()
    {
        return $this->belongsTo(HealthplanAssignedStatus::class, 'healthplan_assigned_status_id');
    }
}

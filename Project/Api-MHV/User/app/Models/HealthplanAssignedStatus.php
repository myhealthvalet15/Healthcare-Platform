<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthplanAssignedStatus extends Model
{
    use HasFactory;

    protected $table = 'healthplan_assigned_status';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'test_code',
        'inserted_on',
        'inserted_by',
        'pending',
        'schedule',
        'in_process',
        'test_completed',
        'result_ready',
        'no_show',
        'certified',
        'cancelled',
    ];

    public function healthplanAssigned()
    {
        return $this->belongsTo(HealthplanAssigned::class, 'test_code', 'test_code');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    protected $table = 'incident_types';

    protected $primaryKey = 'incident_type_id';

    protected $fillable = [
        'incident_type_name',
    ];
}

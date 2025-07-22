<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OhcComponents extends Model
{
    protected $table = 'ohc_components';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'corporate_id',
        'location_id',
        'incident_type_id',
        'injury_color_types',
    ];

    protected $casts = [
        'injury_color_types' => 'array',
    ];

    public function corporate()
    {
        return $this->belongsTo(MasterCorporate::class, 'corporate_id', 'corporate_id');
    }

    public function location()
    {
        return $this->belongsTo(MasterCorporate::class, 'location_id', 'location_id');
    }
}

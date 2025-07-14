<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'corporate_id',
        'event_name',
        'event_description',
        'guest_name',
        'from_datetime',
        'to_datetime'
    ];
    public function details()
    {
        return $this->hasOne(EventDetails::class, 'event_row_id', 'event_id');
    }
    

}

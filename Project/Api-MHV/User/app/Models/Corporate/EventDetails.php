<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Model;

class EventDetails extends Model
{
    protected $table = 'event_details';

    protected $fillable = [
        'corporate_id',
        'event_row_id',
        'employee_type',
        'department',
        'condition',
        'test_taken'
    ];
    protected $casts = [
    'employee_type' => 'array',
    'department'    => 'array',
    'test_taken'    => 'array',
];


    public function event()
    {
        return $this->belongsTo(Event::class, 'event_row_id', 'event_id');
    }
    
    
}

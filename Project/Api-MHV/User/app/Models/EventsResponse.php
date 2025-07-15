<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsResponse extends Model
{
    use HasFactory;

    // Table name (if different from plural of model name)
    protected $table = 'event_responses';

    // Primary key
    protected $primaryKey = 'id';

    // Auto-increment is enabled by default for primary keys
    public $incrementing = true;

    // Timestamps enabled
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'event_id',
        'user_id',
        'corporate_id',
        'status', // enum: yes / no
    ];

    // Cast enum status as string
    protected $casts = [
        'status' => 'string',
    ];

}

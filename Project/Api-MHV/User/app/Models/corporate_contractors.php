<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class corporate_contractors extends Model
{
    use HasFactory;
    protected $table = 'corporate_contractors'; 
    protected $primaryKey = 'corporate_contractors_id';
    protected $fillable = [
        'corporate_contractors_id',
        'contractor_name',
        'email',
        'address',
        'location_id',
        'active_status'
    ];
}

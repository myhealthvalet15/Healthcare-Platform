<?php

namespace App\Models\Others;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporatePO extends Model
{
    use HasFactory;

    protected $table = 'corporate_po'; // Define table name
    protected $primaryKey = 'corporate_po_id';
    protected $fillable = [
        'corporate_id',
        'location_id',
        'corporate_user_id',
        'vendor_name',
        'po_number',
        'po_value',
        'po_date'
             
        
    ];
}

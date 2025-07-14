<?php

namespace App\Models\Others;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'corporate_inventory';
    protected $primaryKey = 'corporate_inventory_id';  
    protected $fillable = [
        'date', 'equipment_name', 'equipment_code', 'equipment_cost', 'calibration_comments', 'manufacturers', 'manufacture_date', 'equipment_lifetime', 'purchase_order', 'vendors',
        'in_use','corporate_id','location_id','calibrated_date','next_calibration_date'
    
    ];
    protected $casts = [
        'calibration_history' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugTemplate extends Model
{
    use HasFactory;
    protected $table = 'drug_template'; 
    protected $primaryKey = 'drug_template_id'; 
    const CREATED_AT = 'created_on';
    const UPDATED_AT = null;
    protected $fillable = [
        'drug_template_id',
        'drug_name',
        'drug_type',
        'drug_manufacturer',
        'drug_ingredient',
        'corporate_id',
        'location_id',
        'ohc',
        'master_pharmacy_id',
        'drug_strength',
        'restock_alert_count',
        'crd',
        'schedule',
        'id_no',
        'hsn_code',
        'amount_per_strip',
        'unit_issue',
        'tablet_in_strip',
        'amount_per_tab',
        'discount',
        'sgst',
        'cgst',
        'igst',
        'bill_status',
        'otc'
        
        
        

    ];

    public $incrementing = false; 
    protected $keyType = 'string';
}

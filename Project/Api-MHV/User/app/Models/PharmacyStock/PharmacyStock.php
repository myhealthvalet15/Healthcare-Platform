<?php

namespace App\Models\PharmacyStock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStock extends Model
{
    protected $table = 'pharmacy_stock';
    protected $primaryKey = 'drug_id';    
    protected $fillable = ['drug_name', 'drug_template_id', 'drug_batch', 'manufacter_date', 
    'expiry_date', 'drug_type', 'drug_strength', 'quantity', 'current_availability', 'sold_quantity',
'ohc','master_pharmacy_id','sgst','cgst','igst','amount_per_tab','total_cost','ohc_pharmacy_id']; 
 
    public $timestamps = true;
}

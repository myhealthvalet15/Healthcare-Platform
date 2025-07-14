<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Model;

class CorporatePharmacy extends Model
{
    protected $table = 'corporate_ohc_pharmacy';
    protected $primaryKey = 'ohc_pharmacy_id';
    protected $fillable = ['pharmacy_name', 'active_status', 'location_id', 'corporate_id','main_pharmacy'];

    public $timestamps = true;
}

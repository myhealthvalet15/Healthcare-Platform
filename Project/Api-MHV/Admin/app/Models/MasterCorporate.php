<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCorporate extends Model
{
    use HasFactory;

    protected $table = 'master_corporate';
    public $timestamps = false;


    protected $fillable = [
        'corporate_id',
        'location_id',
        'corporate_no',
        'corporate_name',
        'display_name',
        'registration_no',
        'industry',
        'industry_segment',
        'prof_image',
        'company_profile',
        'created_by',
        'gstin',
        'discount',
        'created_on',
        'valid_from',
        'valid_upto',
        'corporate_color',
        'active_status',
    ];

    // public function financials()
    // {
    //     return $this->hasOne(CorporateFinancial::class, 'location_id', 'location_id');
    // }

    // public function address()
    // {
    //     return $this->hasOne(CorporateAddress::class, 'location_id', 'location_id');
    // }

    // public function thresholds()
    // {
    //     return $this->hasOne(CorporateThreshold::class, 'location_id', 'location_id');
    // }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCorporateAddress extends Model
{
   // public $timestamps = false;


    protected $table = 'master_corporate_address';

    protected $fillable = [
        'corporate_id',
        'location_id',
        'pincode_id',
        'area_id',
        'pincode_id',
        'country_id',
        'state_id',
        'city_id',
        'area_id',
        'latitude',
        'longitude',
        'website_link',
    ];

    public function masterCorporate()
    {
        return $this->belongsTo(MasterCorporate::class, 'location_id', 'location_id');
    }
    // public function address() {
    //     return $this->belongsTo(Address::class); 
    // }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $table = 'address';
    protected $primaryKey = 'address_id';

    
    protected $fillable = ['address_id', 'address_name', 'address_type','area_id','city_id','state_id','country_id','active_status'];

  
    public $timestamps = false;
    public function corporate_address() {
        return $this->hasMany(CorporateAddress::class); 
    }
}

<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Model;

class CorporateOHC extends Model
{
    protected $table = 'corporate_ohc';
    protected $primaryKey = 'corporate_ohc_id';
    protected $fillable = ['ohc_name', 'active_status', 'location_id', 'corporate_id'];

    public $timestamps = true;
}

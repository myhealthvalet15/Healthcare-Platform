<?php

namespace App\Models\Department;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CorporateHl3 extends Model
{
   use HasFactory;

   protected $table='corporate_hl3';

   protected $primarykey='hl3_id';

   public $incrementing = true;
   
   public $timestamps = true;

   protected $fillable = [
    'hl3_name',
    'h13_code',
    'hl2_id',
    'active_status',
    'corporate_admin_user_id',
];

public function hl2()
{
    return $this->belongsTo(CorporateHl2::class, 'hl2_id');
}

}

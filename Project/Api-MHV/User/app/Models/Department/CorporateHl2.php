<?php

namespace App\Models\Department;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CorporateHl2 extends Model
{
    protected $table = 'corporate_hl2';

   
    protected $primaryKey = 'hl2_id';

    public $incrementing = true;

  
    protected $keyType = 'unsignedBigInteger';

   
    public $timestamps = false;

   
    protected $fillable = [
        'hl2_name',
        'hl2_code',
        'description',
        'hl1_id',
        'active_status',
        'corporate_admin_user_id',
    ];

   
  

    
    public function hl1()
    {
        return $this->belongsTo(CorporateHl1::class, 'hl1_id');
    }
}
